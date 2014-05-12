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
class JSocialJomsocial implements JSocial
{

	public function __construct() {
		if (!$this->checkExists()) {
			throw new Exception('Jomsocial not installed');
		}
		require_once JPATH_SITE . '/components/com_community/libraries/core.php';
	}

	public function getProfileData(JUser $user) {
		return CFactory::getUser($user->id);
	}
	
	public function getProfileUrl(JUser $user) {
		echo CRoute::_('index.php?option=com_community&view=profile&id='.$user->id);
	}
	
	public function getAvatar(JUser $user) {
		return CFactory::getAvatar($user->id);
	}
	public function getFriends(JUser $user, $accepted=true) {}
	public function addFriend(JUser $user, $userid) {}
	public function addStream(JUser $user, $options) {}
	public function setStatus(JUser $user, $status, $options) {}
	public function getRegistrationLink($options) {}
	public function sendMessage(JUser $user, $recepient) {}
	
	public function checkExists()  {
		return JFile::exists(JPATH_SITE.'/components/com_community/libraries/core.php');
	}
}
