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
 * @since       1.0
 */
class JSocialJomsocial implements JSocial
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
			throw new Exception('Jomsocial not installed');
		}

		$this->mainframe = JFactory::getApplication();

		require_once JPATH_SITE . '/components/com_community/libraries/core.php';
	}

	/**
	 * The function to check if Easysocial is installed
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function checkExists()
	{
		return JFile::exists(JPATH_SITE . '/components/com_community/libraries/core.php');
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
		return CFactory::getUser($user->id);
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
		$temp = 'index.php?option=com_community&view=profile&userid=' . $user->id;

		return CRoute::_($temp);
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
		$uimage = '';
		$cuser = CFactory::getUser($user->id);
		$uimage = $cuser->getThumbAvatar();

		if (!$this->mainframe->isSite())
		{
			$uimage = str_replace('administrator/', '', $uimage);
		}

		// +Manoj
		// All other social libs return similar path (absolute URLs for images)
		$findifurl = 'http';
		$ifurl     = strpos($uimage, $findifurl);

		// If no 'http' found in returned path, add it using Juri::root()
		if ($ifurl === false)
		{
			// $uimage = str_replace('/' . basename(JPATH_SITE), '', $uimage);
			$uimage = str_replace(JUri::root(true) . '/', '', $uimage);
			$uimage = JUri::root() . $uimage;
		}

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
		$friendsModel	= CFactory::getModel('Friends');

		/*
		 * Jomsocial's Basic function is like this and supports following parameters
		 * public function getFriends($id, $sorted = 'latest', $useLimit = true , $filter = 'all' , $maxLimit = 0, $namefilter = '' )*/

		$friends		= $friendsModel->getFriends($user->id, 'name', false);
		$newfriends = array();

		if (!empty($friends))
		{
			foreach ($friends as $friend)
			{
				$newfriends[$friend->id] = new stdClass;
				$newfriends[$friend->id]->id = $friend->id;
				$newfriends[$friend->id]->name = $friend->getDisplayName($friend->id);
				$newfriends[$friend->id]->avatar = $friend->getThumbAvatar($friend->id);
			}
		}

		return $newfriends;
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
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__community_connection WHERE connect_from=$connect_to_user->id AND connect_to  = $connect_from_user->id ";
		$db->setQuery($sql);
		$once_done = $db->loadResult();

		if (!$once_done)
		{
			$insertfrnd = new stdClass;
			$insertfrnd->connection_id				=	null;
			$insertfrnd->connect_from				=	$connect_from_user->id;
			$insertfrnd->connect_to 			=	$connect_to_user->id;
			$insertfrnd->status 			=	1;
			$insertfrnd->group			=	0;
			$db->insertObject('#__community_connection', $insertfrnd, 'connection_id');

			$insertfrnds = new stdClass;
			$insertfrnds->connection_id				=	null;
			$insertfrnds->connect_from				=	$connect_to_user->id;
			$insertfrnds->connect_to 			=	$connect_from_user->id;
			$insertfrnds->status 			=	1;
			$insertfrnds->group			=	0;
			$db->insertObject('#__community_connection', $insertfrnds, 'connection_id');
		}

		// Increase friend count of inviter and invitee
		$query = "UPDATE `#__community_users`
		SET `friendcount`=`friendcount`+1
		WHERE `userid`= '" . $connect_to_user->id . "'";
		$db->setQuery($query);
		$db->query();

		$query = "UPDATE `#__community_users`
		SET `friendcount`=`friendcount`+1
		WHERE `userid`= '" . $connect_from_user->id . "'";
		$db->setQuery($query);
		$db->query();
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
		$toolbar    = CFactory::getToolbar();
		$tool = CToolbarLibrary::getInstance();

		return "<div id='community-wrap' class='jomsocial-wrapper'>" . $tool->getHTML() . '</div>';
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
	public function pushActivity($actor_id, $act_type, $act_subtype, $act_description, $act_link, $act_title, $act_access)
	{
		/*load Jomsocial core*/
		$linkHTML = '';

		// Push activity
		if ($act_title and $act_link)
		{
			$linkHTML = '<a href="' . $act_link . '">' . $act_title . '</a>';
		}

		$act = new stdClass;
		$act->cmd = 'wall.write';
		$act->actor = $actor_id;
		$act->target = 0;
		$act->title = '{actor} ' . $act_description . ' ' . $linkHTML;
		$act->content = '';
		$act->app = 'wall';
		$act->cid = 0;
		$act->access = $act_access;

		CFactory::load('libraries', 'activities');

		if (defined('CActivities::COMMENT_SELF'))
		{
			$act->comment_id = CActivities::COMMENT_SELF;
			$act->comment_type = 'profile.location';
		}

		if (defined('CActivities::LIKE_SELF'))
		{
				$act->like_id = CActivities::LIKE_SELF;
				$act->like_type = 'profile.location';
		}

		$res = CActivityStream::add($act);

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
		/*load Jomsocial core*/
		$act = new stdClass;
		$act->actor = $streamOption['actorId'];
		$act->target = $streamOption['targetId'];

		$actor = CFactory::getUser($act->actor);
		$actorLink = '<a class="cStream-Author" href="' . CUrlHelper::userLink($actor->id) . '">' . $actor->getDisplayName() . '</a>';

		$act->title = $actorLink . $streamOption['elementInfo']->html;
		$act->content = $streamOption['content'];
		$act->app = $streamOption['contextType'] . '.' . strtolower($streamOption['action']);
		$act->cid = 0;
		$act->access = $streamOption['actAccess'];

		if (isset($streamOption['elementInfo']->params))
		{
			$act->params = json_encode($streamOption['elementInfo']->params);
		}

		CFactory::load('libraries', 'activities');

		if (defined('CActivities::COMMENT_SELF'))
		{
			$act->comment_id = CActivities::COMMENT_SELF;
			$act->comment_type = 'profile.location';
		}

		if (defined('CActivities::LIKE_SELF'))
		{
				$act->like_id = CActivities::LIKE_SELF;
				$act->like_type = 'profile.location';
		}

		$res = CActivityStream::add($act);

		return true;
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
	 * The function to get registartion link for Easysocial
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
		CFactory::load('libraries', 'userpoints');
		CFactory::load('libraries', 'notification');
		CUserPoints::assignPoint('inbox.message.send');

		$actor = CFactory::getUser($sender->id);

		// Add notification
		$params = new CParameter('');

		if (isset($options['params']['url']))
		{
			$params->set('url', $options['params']['url']);
		}

		$params->set('actor', $actor->getDisplayName());
		$params->set('actor_url', 'index.php?option=com_community&view=profile&userid=' . $actor->id);

		/*$params->set( 'message' , $data['body'] );
		$params->set( 'title'	, $data['subject'] );
		$my=CFactory::getUser();
		CNotificationLibrary::add(
		* 							'etype_inbox_create_message', $my->id , $data[ 'to' ],
		* 								JText::sprintf('COM_COMMUNITY_SENT_YOU_MESSAGE', $my->getDisplayName()) , '' , 'inbox.sent' , $params
		* 							);*/

		$model = CFactory::getModel('Notification');
		$model->add($sender->id, $receiver->id, $content, $options['cmd'], $options['type'], $params);
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
		CFactory::load('libraries', 'userpoints');
		CFactory::load('libraries', 'notification');
		CuserPoints::assignPoint($options['command'], $receiver->id);
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
	public function createGroup($data, $options = array())
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		$my     = CFactory::getUser();
		$config = CFactory::getConfig();
		$group  = JTable::getInstance('Group', 'CTable');

		$group->name        = $data['title'];
		$group->description = $data['short_desc'];

		// Category Id must not be empty and will cause failure on this group if its empty.
		$group->categoryid  = $options['catId'];
		$group->website     = '';
		$group->ownerid     = $my->id;
		$group->created     = gmdate('Y-m-d H:i:s');
		$group->approvals   = 0;

		$params = new CParameter('');

		// Here you need some code from private _bindParams()
		$group->params    = $params->toString();
		$group->published = ($config->get('moderategroupcreation')) ? 0 : 1;
		$group->store();

		return $group->id;
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
	public function addMemberToGroup($groupId, JUser $groupmember, $state = 1)
	{
		require_once JPATH_ROOT . '/components/com_community/libraries/core.php';

		// Into the groups members table
		$member           = JTable::getInstance('GroupMembers', 'CTable');
		$member->groupid  = $groupId;
		$member->memberid = $groupmember->id;

		// Creator should always be 1 as approved as they are the creator.
		$member->approved = 1;

		// @todo: Setup required permissions in the future
		$member->permissions = '1';
		$member->store();

		return true;
	}
}
