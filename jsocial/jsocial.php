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
interface JSocial
{
	public function getProfileData(JUser $user);
	public function getProfileUrl(JUser $user);
	public function getAvatar(JUser $user);
	public function getFriends(JUser $user, $accepted=true);
	
	public function addFriend(JUser $user, $userid);
	public function addStream(JUser $user, $options);
	
	public function setStatus(JUser $user, $status, $options);
	
	public function sendMessage(JUser $user, $recipent);
	
	public function getRegistrationLink($options);
	public function checkExists();
}
