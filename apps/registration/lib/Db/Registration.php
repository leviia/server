<?php

declare(strict_types=1);
/**
 * @copyright Copyright (c) 2017 Julius Härtl <jus@bitgrid.net>
 *
 * @author Julius Härtl <jus@bitgrid.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\Registration\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method string getEmail()
 * @method void setEmail(string $email)
 * @method string getUsername()
 * @method void setUsername(string $username)
 * @method string getPassword()
 * @method void setPassword(string $password)
 * @method string getDisplayname()
 * @method void setDisplayname(string $displayname)
 * @method bool getEmailConfirmed()
 * @method void setEmailConfirmed(bool $emailConfirmed)
 * @method string getToken()
 * @method void setToken(string $token)
 * @method string getClientSecret()
 * @method void setClientSecret(string $clientSecret)
 * @method string getRequested()
 * @method void setRequested(string $requested)
 */
class Registration extends Entity {
	public $id;
	protected $email;
	protected $username;
	protected $displayname;
	protected $password;
	protected $token;
	protected $requested;
	protected $emailConfirmed;
	protected $clientSecret;

	public function __construct() {
		$this->addType('email', 'string');
		$this->addType('username', 'string');
		$this->addType('password', 'string');
		$this->addType('displayname', 'string');
		$this->addType('emailConfirmed', 'boolean');
		$this->addType('token', 'string');
		$this->addType('clientSecret', 'string');
		$this->addType('requested', 'string'); // TODO datetime is not supported?
	}
}
