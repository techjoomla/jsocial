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
class JSocialAlphauserpoints implements JSocial
{
	private $gravatar = true;

	private $gravatar_surl = 'https://secure.gravatar.com/avatar/';

	private $gravatar_url = 'http://www.gravatar.com/avatar/';

	private $gravatar_size = 200;

	private $gravatar_default = '';

	private $gravatar_rating = 'g';

	private $gravatar_secure = false;

	/**
	 * The constructor
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		if (JFile::exists(JPATH_SITE . '/components/com_alphauserpoints/helper.php'))
		{
			require_once JPATH_SITE . '/components/com_alphauserpoints/helper.php';
		}

		if (JFile::exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
		{
			require_once JPATH_SITE . '/components/com_altauserpoints/helper.php';
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
		return $user;
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
		return;
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
		if (!$this->gravatar)
		{
			return;
		}

		return $this->gravatarURL($user->email);
	}

	/**
	 * The function to get friends of a User
	 *
	 * @param   MIXED  $user      JUser Objcet
	 * @param   INT    $accepted  Optional param, bydefault true to get only friends with request accepted
	 * @param   INT    $options   Optional array.. Extra options to pass to the getFriends Query :
	 * state, limit and idonly(if idonly only ids array will be returned) are supported
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
	 * @param   INT     $user     User against whom activity is added
	 * @param   STRING  $options  Activity options
	 *
	 * @return  true
	 *
	 * @since  1.0
	 */
	public function addStream(JUser $user, $options)
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
	public function setStatus(JUser $user, $status, $options=array())
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
	public function getRegistrationLink($options=array())
	{
		return JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute());
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
	 * get Avatar from Gravtar
	 *
	 * @param   STRING  $email  email of the user
	 *
	 * @return  STRING
	 *
	 * @since 1.0
	 */
	private function gravatarURL ($email)
	{
		$url = ($this->gravatar_secure) ? $this->gravatar_surl : $this->gravatar_url;
		$url .= md5($email) . '?d=' . $this->gravatar_default . '&rating=' . $this->gravatar_rating . '&s=' . $this->gravatar_size;

		return $url;
	}

	/**
	 * The function to check if Alpha user points is installed
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function checkExists()
	{
		if (JFile::exists(JPATH_SITE . '/components/com_alphauserpoints/helper.php'))
		{
			return true;
		}
		elseif(JFile::exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Helper function to get ReferreID
	 *
	 * @param   OBJECT  $user  User
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function getAnyUserReferreID(JUser $user)
	{
		if (JFile::exists(JPATH_SITE . '/components/com_alphauserpoints/helper.php'))
		{
			$res = AlphaUserPointsHelper::getAnyUserReferreID($user->id);
		}
		elseif (JFile::exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
		{
			$res = AltaUserPointsHelper::getAnyUserReferreID($user->id);
		}

		return $res;
	}

	/**
	 * The function add points to user
	 *
	 * @param   MIXED  $user     User to whom points to be added
	 * @param   ARRAY  $options  is array
	 *
	 * $options[command] for example invites sent
	 * options[extension] for example com_invitex
	 *
	 * @return ARRAY success 0 or 1
	 */
	public function addpoints(JUser $user, $options = array())
	{
		$function = $options['plugin_function'];
		$referre = $options['referrerid'];
		$key = $options['keyreference'];
		$date = $options['datareference'];
		$randompoints	=	$options['randompoints'];
		$feedback = $options['feedback'];
		$force = $options['force'];
		$frontmessage = $options['frontmessage'];

		if (JFile::exists(JPATH_SITE . '/components/com_altauserpoints/helper.php'))
		{
			$res = AltaUserPointsHelper::newpoints($function, $referre, $key, $date, $randompoints, $feedback, $force, $frontmessage);
		}
		elseif (JFile::exists(JPATH_SITE . '/components/com_alphauserpoints/helper.php'))
		{
			$res = AlphaUserPointsHelper::newpoints($function, $referre, $key, $date, $randompoints, $feedback, $force, $frontmessage);
		}

		return $res;
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
