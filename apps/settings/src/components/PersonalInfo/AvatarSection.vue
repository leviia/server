<!--
	- @copyright 2022 Christopher Ng <chrng8@gmail.com>
	-
	- @author Christopher Ng <chrng8@gmail.com>
	-
	- @license AGPL-3.0-or-later
	-
	- This program is free software: you can redistribute it and/or modify
	- it under the terms of the GNU Affero General Public License as
	- published by the Free Software Foundation, either version 3 of the
	- License, or (at your option) any later version.
	-
	- This program is distributed in the hope that it will be useful,
	- but WITHOUT ANY WARRANTY; without even the implied warranty of
	- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	- GNU Affero General Public License for more details.
	-
	- You should have received a copy of the GNU Affero General Public License
	- along with this program. If not, see <http://www.gnu.org/licenses/>.
	-
-->

<template>
	<div>
		<HeaderBar :input-id="inputId"
			:readable="avatar.readable"
			:scope.sync="avatar.scope" />

		<div v-if="!imgSrc" class="avatar__preview">
			<span v-if="loading" class="icon-loading" />
			<img v-else
				class="cropped-image"
				:src="avatarUrl">

			<template v-if="avatarChangeSupported">
				<div class="avatar__buttons">
					<Button :aria-label="t('core', 'Upload profile picture')"
						@click="showFileChooser">
						<template #icon>
							<Upload :size="20" />
						</template>
					</Button>
					<Button :aria-label="t('core', 'Select from files')"
						@click="showFilePickerDialog">
						<template #icon>
							<Folder :size="20" />
						</template>
					</Button>
					<Button :aria-label="t('core', 'Remove profile picture')"
						@click="removeAvatar">
						<template #icon>
							<Delete :size="20" />
						</template>
					</Button>
				</div>
				<p><em>{{ t('core', 'png or jpg, max. 20 MB') }}</em></p>
			</template>
			<span v-else>
				{{ t('settings', 'Picture provided by original account') }}
			</span>
		</div>

		<div v-else class="avatar-crop">
			<div class="crop-area">
				<VueCropper
					ref="cropper"
					:aspect-ratio="1 / 1"
					:src="imgSrc"
					preview=".preview" />
			</div>
			<Button @click="imgSrc = null">
				{{ t('core', 'Cancel') }}
			</Button>
			<Button type="primary"
				@click="cropImage">
				{{ t('core', 'Set avatar') }}
			</Button>
		</div>

		<input ref="input"
			type="file"
			name="image"
			accept="image/*"
			@change="setImage">
	</div>
</template>

<script>
import Avatar from '@nextcloud/vue/dist/Components/Avatar'
import Button from '@nextcloud/vue/dist/Components/Button'
import VueCropper from 'vue-cropperjs'

import axios from '@nextcloud/axios'
import { getFilePickerBuilder } from '@nextcloud/dialogs'
import { getCurrentUser } from '@nextcloud/auth'
import { generateUrl } from '@nextcloud/router'
import { loadState } from '@nextcloud/initial-state'
import 'cropperjs/dist/cropper.css'

import Upload from 'vue-material-design-icons/Upload'
import Folder from 'vue-material-design-icons/Folder'
import Delete from 'vue-material-design-icons/Delete'

import HeaderBar from './shared/HeaderBar.vue'
import { ACCOUNT_PROPERTY_ENUM, NAME_READABLE_ENUM } from '../../constants/AccountPropertyConstants.js'

const { avatar } = loadState('settings', 'personalInfoParameters', {})
const { avatarChangeSupported } = loadState('settings', 'accountParameters', {})

const picker = getFilePickerBuilder(t('settings', 'Select profile picture'))
	.setMultiSelect(false)
	.setModal(true)
	.setType(1)
	.allowDirectories()
	.build()

export default {
	name: 'AvatarSection',

	components: {
		Avatar,
		Button,
		HeaderBar,
		VueCropper,
		Upload,
		Folder,
		Delete,
	},

	data() {
		return {
			avatar: { ...avatar, readable: NAME_READABLE_ENUM[avatar.name] },
			avatarChangeSupported,
			avatarUrl: null,
			data: null,
			imgSrc: null,
			loading: false,
		}
	},

	beforeMount() {
		this.updateAvatar()
	},

	computed: {
		inputId() {
			return `account-property-${ACCOUNT_PROPERTY_ENUM.AVATAR}`
		},
	},

	methods: {
		cropImage() {
			this.imgSrc = null
			this.saveAvatar()
		},

		setImage(e) {
			const file = e.target.files[0]
			if (file.type.indexOf('image/') === -1) {
				alert('Please select an image file')
				return
			}
			if (typeof FileReader === 'function') {
				const reader = new FileReader()
				reader.onload = (event) => {
					this.imgSrc = event.target.result
					this.$nextTick(() => this.$refs.cropper.replace(event.target.result))
				}
				reader.readAsDataURL(file)
			} else {
				alert('Sorry, FileReader API not supported')
			}
		},

		showFileChooser() {
			this.$refs.input.click()
		},

		saveAvatar() {
			this.loading = true
			this.$refs.cropper.getCroppedCanvas().toBlob((blob) => {
				const formData = new FormData()
				formData.append('files[]', blob)
				axios.post(generateUrl('/avatar/'), formData, {
					headers: {
						'Content-Type': 'multipart/form-data',
					},
				}).then(() => {
					this.updateAvatar()
				})
			})
		},

		async showFilePickerDialog() {
			const path = await picker.pick()
			await axios.post(generateUrl('/avatar/'), { path })
			this.imgSrc = generateUrl('/avatar/tmp') + '?requesttoken=' + encodeURIComponent(OC.requestToken) + '#' + Math.floor(Math.random() * 1000)
		},

		updateAvatar() {
			this.loading = true
			const newAvatarUrl = generateUrl('/avatar/') + getCurrentUser().uid + '/256?v=' + Date.now()
			const img = new Image()
			img.onload = () => {
				this.loading = false
				this.avatarUrl = newAvatarUrl
				oc_userconfig.avatar.version = Date.now()
			}
			img.src = newAvatarUrl
			// FIXME emit an event to update all avatars on the page here
		},

		async removeAvatar() {
			this.loading = true
			await axios.delete(generateUrl('/avatar/'))
			window.oc_userconfig.avatar.generated = true
			this.updateAvatar()
		},
	},
}
</script>

<style lang="scss" scoped>
input[type="file"] {
	display: none;
}

.crop-area, .cropped-image {
	width: 300px;
}

.avatar {
	&__preview {
		display: flex;
		flex-direction: column;
		align-items: center;
		width: 300px;

		.cropped-image {
			width: 200px;
			height: 200px;
			border-radius: 50%;
			overflow: hidden;
			margin-bottom: 12px;
		}
	}

	&__buttons {
		display: flex;
		gap: 0 10px;
	}
}

img {
	width: 100%;
}

.crop-placeholder {
	width: 300px;
	height: 300px;
	border-radius: 50%;
	background: #ccc;
}

::v-deep .cropper-view-box {
	border-radius: 50%;
}
</style>
