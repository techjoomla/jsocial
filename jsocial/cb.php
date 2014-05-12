<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

/**
 * Interface to handle Social Extensions
 *
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 * @since       3.1
 */
class JSocialCB implements JSocial
{

	public function __construct() {
		if (!$this->checkExists()) {
			throw new Exception('Community Builder is not Installed');
		}
	}

	public function getProfileData(JUser $user) {
	
	}
	
	public function getProfileUrl(JUser $user) {}
	public function getAvatar(JUser $user) {}
	public function getFriends(JUser $user, $accepted=true) {}
	public function addFriend(JUser $user, $userid) {}
	public function addStream(JUser $user, $options) {}
	public function setStatus(JUser $user, $status, $options) {}
	public function getRegistrationLink($options) {}
	public function sendMessage(JUser $user, $recepient) {}
		
	public function checkExists()  {
		return JFolder::exists(JPATH_SITE.'/components/com_comprofiler');
	}
}
