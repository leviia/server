<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Joas Schilling <coding@schilljs.com>
 *
 * @author Joas Schilling <coding@schilljs.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\QuotaWarning\AppInfo;

use OCA\QuotaWarning\Job\User;
use OCA\QuotaWarning\Notification\Notifier;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Util;

class Application extends App implements IBootstrap {
	public const APP_ID = 'quota_warning';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}

	public function register(IRegistrationContext $context): void {
	}

	public function boot(IBootContext $context): void {
		$this->registerLoginHook();
		$this->registerNotifier();
	}


	protected function registerLoginHook(): void {
		Util::connectHook('OC_User', 'post_login', $this, 'loginHook');
	}

	public function loginHook(array $params): void {
		if (!isset($params['uid'])) {
			return;
		}

		$jobList = $this->getContainer()->getServer()->getJobList();
		$jobList->add(
			User::class,
			['uid' => $params['uid']]
		);
	}

	public function registerNotifier(): void {
		$notificationManager = $this->getContainer()->getServer()->getNotificationManager();
		$notificationManager->registerNotifierService(Notifier::class);
	}
}
