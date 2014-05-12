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
class JSocialJoomla implements JSocial
{

	var $gravatar = true;
	
	var $gravatar_surl = 'https://secure.gravatar.com/avatar/';

	var $gravatar_url = 'http://www.gravatar.com/avatar/';
	
	var $gravatar_size = 200;
	
	var $gravatar_default = '';
	
	var $gravatar_rating = 'g';
	
	var $gravatar_secure = false;
	
	public function __construct() {
	}

	public function getProfileData(JUser $user) {
		return $user;
	}
	
	public function getProfileUrl(JUser $user) {
		return;
	}
	
	public function getAvatar(JUser $user) {
		if (!$this->gravatar) { return; }
		
		return $this->gravatarURL($user->email);
	}
	
	public function getFriends(JUser $user, $accepted=true) {}
	public function addFriend(JUser $user, $userid) {}
	public function addStream(JUser $user, $options) {}
	public function setStatus(JUser $user, $status, $options=array()) {}
	
	public function getRegistrationLink($options=array()) {
		return JRoute::_('index.php?option=com_users&view=registration&Itemid=');
	}
	
	public function sendMessage(JUser $user, $recepient) {}
	
	private function gravatarURL ($email) {
		$url = ($this->gravatar_secure) ? $this->gravatar_surl : $this->gravatar_url;
		$url .= md5($email) . '?d=' . $this->gravatar_default . '&rating=' . $this->gravatar_rating . '&s=' . $this->gravatar_size;
		
		return $url;
	}
		
	public function checkExists()  {
		return JFolder::exists(JPATH_SITE.'/components/com_comprofiler');
	}
}
