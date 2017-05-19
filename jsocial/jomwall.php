<?php
/**
 * @version     SVN: <svn_id>
 * @package     Techjoomla.Libraries
 * @subpackage  JSocial
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

defined('JPATH_BASE') or die;
jimport('joomla.filesystem.file');

/**
 * Interface to handle Social Extensions
 *
 * @package     Joomla.Libraries
 * @subpackage  JSocial
 * @since       3.1
 */
class JSocialJomwall implements JSocial
{
	/**
	 * The constructor
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		if (!$this->checkExists())
		{
			throw new Exception('Jomwall is not Installed');
		}
	}

	/**
	 * The function to get profile data of User
	 *
	 * @param   MIXED  $user  JUser Objcet
	 *
	 * @return  JUser Objcet
	 *
	 * @since   1.0
	 */
	public function getProfileData(JUser $user)
	{
	}

	/**
	 * The function to get profile link User
	 *
	 * @param   MIXED  $user  JUser Objcet
	 *
	 * @return  STRING
	 *
	 * @since   1.0
	 */
	public function getProfileUrl(JUser $user)
	{
		$awduser = new AwdwallHelperUser;
		$Itemid = $awduser->getComItemId();
		$link = JRoute::_('index.php?option=com_awdwall&view=awdwall&layout=mywall&wuid=' . $user->id . '&Itemid=' . $Itemid);
	}

	/**
	 * The function to get profile AVATAR of a User
	 *
	 * @param   MIXED  $user           JUser Objcet
	 *
	 * @param   INT    $gravatar_size  Size of the AVATAR
	 *
	 * @return  STRING
	 *
	 * @since   1.0
	 */
	public function getAvatar(JUser $user, $gravatar_size = '')
	{
		$awduser = new AwdwallHelperUser;
		$uimage = $awduser->getAvatar($user->id);

		return $uimage;
	}

	/**
	 * The function to get friends of a User
	 *
	 * @param   MIXED  $user      JUser Objcet
	 * @param   INT    $accepted  Optional param, bydefault true to get only friends with request accepted
	 * @param   INT    $options   Optional array.. Extra options to pass to the getFriends Query
	 *  : state, limit and idonly(if idonly only ids array will be returned) are supported
	 *
	 * @return  Friends objects
	 *
	 * @since   1.0
	 */
	public function getFriends(JUser $user, $accepted=true, $options = array())
	{
	}

	/**
	 * The function to add provided users as Friends
	 *
	 * @param   MIXED  $connect_from_user  User who is requesting connection
	 * @param   INT    $connect_to_user    User whom to request
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addFriend(JUser $connect_from_user, JUser $connect_to_user)
	{
	}

	/**
	 * The function to get Easysocial toolbar
	 *
	 * @return  toolbar HTML
	 *
	 * @since   1.0
	 */
	public function getToolbar()
	{
	}

	/**
	 * Add activity stream
	 *
	 * @param   INT     $actor_id         User against whom activity is added
	 * @param   STRING  $act_type         type of activity
	 * @param   STRING  $act_subtype      sub type of activity
	 * @param   STRING  $act_description  Activity description
	 * @param   STRING  $act_link         LInk of Activity
	 * @param   STRING  $act_title        Title of Activity
	 * @param   STRING  $act_access       Access level
	 *
	 * @return  true
	 *
	 * @since  1.0
	 */
	public function pushActivity($actor_id, $act_type, $act_subtype='', $act_description='', $act_link='', $act_title='', $act_access='')
	{
		/* Load jomwall core*/
		if (!class_exists('AwdwallHelperUser'))
		{
			require_once JPATH_SITE . '/components/com_awdwall/helpers/user.php';
		}

		$linkHTML = '<a href="' . $act_link . '">' . $act_title . '</a>';
		$comment = $act_description . ' ' . $linkHTML;
		$attachment = $act_link;
		$type = 'text';
		$imgpath = null;
		$params = array();

		AwdwallHelperUser::addtostream($comment, $attachment, $type, $actor_id, $imgpath, $params);

		return true;
	}

	/**
	 * The function to add stream
	 *
	 * @param   Array  $streamOption  Stram array
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function advPushActivity($streamOption)
	{
	}

	/**
	 * The function to set status of a user
	 *
	 * @param   MIXED   $user     User whose status is to be set
	 * @param   STRING  $status   status to be set
	 * @param   MIXED   $options  status to be set
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setStatus(JUser $user, $status, $options)
	{
	}

	/**
	 * The function to get registartion link for CB
	 *
	 * @param   ARRAY  $options  options
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function getRegistrationLink($options)
	{
	}

	/**
	 * Send Message
	 *
	 * @param   OBJECT  $user       User who is sending Message
	 * @param   OBJECT  $recepient  User to whom Message is to send
	 *
	 * @return  boolean
	 *
	 * @since  1.0
	 */
	public function sendMessage(JUser $user, $recepient)
	{
	}

	/**
	 * The function to check if CB is installed
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function checkExists()
	{
		return JFile::exists(JPATH_SITE . '/components/com_awdwall/helpers/user.php');
	}

	/**
	 * The function add points to user
	 *
	 * @param   MIXED  $receiver  User to whom points to be added
	 * @param   ARRAY  $options   is array
	 *
	 * $options[command] for example invites sent
	 * options[extension] for example com_invitex
	 *
	 * @return ARRAY success 0 or 1
	 */
	public function addpoints(JUser $receiver,$options=array())
	{
	}

	/**
	 * Send Notification
	 *
	 * @param   OBJECT  $sender        User who is sending notification
	 * @param   OBJECT  $receiver      User to whom notification is to send
	 * @param   STRING  $content       Main content of the notification
	 * @param   STRING  $options       Optional options
	 * @param   STRING  $emailOptions  Email options. If you do not want to send email, $emailOptions should be set to false
	 *
	 * @return  boolean
	 *
	 * @since  1.0
	 */
	public function sendNotification(JUser $sender, JUser $receiver, $content = "JS Notification", $options = array(), $emailOptions = false)
	{
	}

	/**
	 * The function to create a group
	 *
	 * @param   ARRAY  $data     Data
	 * @param   ARRAY  $options  Additional data
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function createGroup($data, $options=array())
	{
	}

	/**
	 * The function to add member to a group
	 *
	 * @param   ARRAY   $groupId      Data
	 * @param   OBJECT  $groupmember  User object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addMemberToGroup($groupId, JUser $groupmember)
	{
	}
}
