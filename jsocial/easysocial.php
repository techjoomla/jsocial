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
class JSocialEasysocial implements JSocial
{
	/**
	 * The constructor
	 *
	 * @since  1.0
	 */
	public function __construct()
	{
		$this->db = JFactory::getDBO();

		if (!$this->checkExists())
		{
			throw new Exception('Easysocial not installed');
		}

		require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';
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
		return $user = Foundry::user($user->id);
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
		$user = Foundry::user($user->id);

		return $link = JRoute::_($user->getPermalink('', true));
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
		$user   = Foundry::user($user->id);
		$uimage = $user->getAvatar();

		return $uimage;
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
		$model = FD::model('friends');

		if (!isset($options['idonly']))
		{
			$options['idonly'] = 1;
		}

		$EasySocialModelFriends = new EasySocialModelFriends;
		$esfriends_ids = $model->getFriends($user->id, $options);

		$esfriends = array();

		if (!empty($esfriends_ids))
		{
			foreach ($esfriends_ids AS $id)
			{
				$user = Foundry::user($id);
				$esfriends[$id] = new StdClass;
				$esfriends[$id]->id = $id;
				$esfriends[$id]->name = $user->name;
				$esfriends[$id]->avatar = $this->getAvatar($user);
				$esfriends[$id]->profile_link = $this->getProfileUrl($user);
			}
		}

		return $esfriends;
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
		$this->db = JFactory::getDBO();
		$sql = "SELECT * FROM #__social_friends
				WHERE (actor_id=" . $connect_from_user->id . " AND target_id  = " . $connect_to_user->id . ")
				OR (target_id=" . $connect_from_user->id . " AND actor_id  = " . $connect_to_user->id . ")";
		$this->db->setQuery($sql);
		$once_done = $this->db->loadResult();

		if (!$once_done)
		{
			$insertfrnd = new stdClass;
			$insertfrnd->id	       =	'';
			$insertfrnd->actor_id  =	$connect_to_user->id;
			$insertfrnd->target_id =	$connect_from_user->id;
			$insertfrnd->state 	   =	1;
			$insertfrnd->created   =	date('y-m-d h:i:s');
			$insertfrnd->modified  =	date('y-m-d h:i:s');
			$this->db->insertObject('#__social_friends', $insertfrnd, 'id');
		}
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
		Foundry::document()->init();

		require_once JPATH_ROOT . '/libraries/techjoomla/common.php';

		$TechjoomlaCommon = new TechjoomlaCommon;

		$easySocialVersion = $TechjoomlaCommon->getExtensionVersion('component', 'com_easysocial');

		if (version_compare($easySocialVersion, '2.0.0', 'ge'))
		{
			return '<div id="es" class="es-frontend es-main  view-profile " data-es-structure="">' . FD::toolbar()->render() . '</div>';
		}
		else
		{
			return '<div id="fd" class="es es-main">' . FD::toolbar()->render() . '</div>';
		}
	}

	/**
	 * The function to add strem
	 *
	 * @param   MIXED  $options  User options
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addStream(JUser $options)
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
	 * The function to check if Easysocial is installed
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function checkExists()
	{
		return JFile::exists(JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php');
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
	public function addpoints(JUser $receiver, $options = array())
	{
		if (empty($options['command']) or empty($options['extension']))
		{
				$return['success'] = 0;
				$return['message'] = 'Command or extension not passed to ';

				return $return;
		}

		$return['success'] = Foundry::points()->assign($options['command'], $options['extension'], $receiver->id);
	}

	/**
	 * Add a badges to a user
	 *
	 * @param   OBJECT  $receiver  User to whom badges are assigned
	 * @param   OBJECT  $options   optional array
	 *
	 * @return array success 0 or 1
	 *
	 * @since  1.0.0
	 */
	public function addbadges(JUser $receiver, $options = array())
	{
		if (empty($options['command']) or empty($options['extension']))
		{
				$return['success'] = 0;
				$return['message'] = 'Command or extension not passed to ';

				return $return;
		}

		$return['success'] = Foundry::badges()->log($options['extension'], $options['command'], $receiver->id, '');
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
	public function sendNotification(JUser $sender, JUser $receiver, $content = "ES Notification", $options = array(), $emailOptions = false)
	{
		$systemOptions = $options;
		$recipient[]   = $receiver->id;

		/* If you do not want to send system notifications, set this to false.
		// $systemOptions - The internal system notifications
		// System notification template*/
		$myUser = Foundry::user($receiver->id);

		$cmd = $systemOptions['cmd'];
		unset($systemOptions['cmd']);

		if (!isset($systemOptions['url']))
		{
			$systemOptions['url'] = JRoute::_($myUser->getPermalink());
		}

		return $resp = Foundry::notify($cmd, $recipient, $emailOptions, $systemOptions);
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
		if ($actor_id != 0)
		{
			$myUser = Foundry::user($actor_id);
		}

		// SOCIAL_TYPE_USERS
		$context = "all";

		$stream = Foundry::stream();
		$template = $stream->getTemplate();
		$template->setActor($actor_id, SOCIAL_TYPE_USER);

		// $template->setContext( $actor_id, SOCIAL_TYPE_USERS );
		$template->setContext($actor_id, $context);
		$template->setVerb('invite');
		$template->setType(SOCIAL_STREAM_DISPLAY_MINI);

		if ($actor_id != 0)
		{
			$userProfileLink = '<a href="' . $myUser->getPermalink('', true) . '">' . $myUser->getName() . '</a>';
			$title 	 = ($userProfileLink . " " . $act_description);
		}
		else
		{
			$title 	 = ("A guest " . $act_description);
		}

		$template->setTitle($title);
		$template->setAggregate(false);

		$template->setPublicStream('core.view');
		$stream->add($template);

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
		// This is the main engine file of EasySocial.
		require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/easysocial.php';

		if ($streamOption['actorId'] != 0)
		{
			$myUser = Foundry::user($streamOption['actorId']);
		}

		// Retrieve the stream library.
		$stream = ES::stream();

		// Retrieve the stream template.
		$template = $stream->getTemplate();

		// @required : Set the actor that is generating this stream item.
		$template->setActor($streamOption['actorId'], SOCIAL_TYPE_USER);

		// @required : Set the context type fo this stream item.
		$template->setContext($streamOption['elementInfo']->id, $streamOption['contextType']);

		/*// @optional : Set the type of the stream, whether it should be rendered in full mode or mini mode.
		// Mini mode does not have a header and does not actions on the stream.
		// Example types: full,mini*/
		if (isset($streamOption['actType']))
		{
			$template->setType($streamOption['actType']);
		}

		// @optional : If you do not plan to create an application to manipulate the stream, you may specify the title here.
		// Example: "Jennifer created a new textbook."
		if (isset($streamOption['title']))
		{
			$template->setTitle($streamOption['title']);
		}

		// @optional : If you do not plan to create an application to manipulate the stream, you may specify the contents of the stream here.
		// Example: "Some descriptions of the book."
		if (isset($streamOption['content']))
		{
			$template->setContent($streamOption['content']);
		}

		// @optional : Set the target item. This is usually needed if you are targeting an object.
		// Example targets: Adam posted an update on Jennifer's profile. The $targetId would be Jennifer's id.
		if (isset($streamOption['targetId']))
		{
			$template->setTarget($streamOption['targetId']);
		}

		// @required : Set the verb/action for this stream item.
		// Example verbs: add, edit , create , update , delete.
		$template->setVerb($streamOption['action']);

		// When we are done with the template, add it through the stream.
		$stream->add($template);
	}

	/**
	 * Helper functions to merge the arrays
	 *
	 * @param   array  $arrays  arrays to be merged
	 *
	 * @return  array
	 *
	 * @since  1.0
	 */
	public function umerge($arrays)
	{
		$result = array();

		foreach ($arrays as $array)
		{
			$array = (array) $array;

			foreach ($array as $value)
			{
				if (array_search($value, $result) === false)
				{
					$result[] = $value;
				}
			}
		}

		return $result;
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
		require_once JPATH_ROOT . '/administrator/components/com_easysocial/includes/foundry.php';

		$db = JFactory::getDbo();

		// Set the category id for the group
		$creator_uid = Foundry::user();

		$esGroup = FD::table('Cluster');

		$esGroup->category_id       = $options['catId'];
		$esGroup->cluster_type      = SOCIAL_TYPE_GROUP;
		$esGroup->creator_type      = SOCIAL_TYPE_USER;

		// Check if uid is passed through data
		if ($data['uid'])
		{
			$esGroup->creator_uid   = $data['uid'];
		}
		else
		{
			$esGroup->creator_uid   = $creator_uid->id;
		}

		$esGroup->title             = $data['title'];

		if (isset($data['group_id']))
		{
			$esGroup->id                = $data['group_id'];
		}

		$esGroup->description       = $data['description'];
		$esGroup->alias             = JFilterOutput::stringURLSafe($data['title']);
		$esGroup->state             = 1;
		$esGroup->created           = JFactory::getDate()->toSQL();
		$esGroup->params            = null;
		$esGroup->hits              = 0;

		// Check if type is passed through data e.g. type = 3 for Invite only groups
		if ($data['type'])
		{
			$esGroup->type          = $data['type'];
		}
		else
		{
			$esGroup->type          = SOCIAL_TYPE_USER;
		}

		$esGroup->key               = md5(Foundry::date()->toSql() . $creator_uid->password . uniqid());

		$esGroup->store();

		return $esGroup->id;
	}

	/**
	 * The function to add member to a group
	 *
	 * @param   ARRAY   $groupId      Data
	 * @param   OBJECT  $groupmember  User object
	 * @param   OBJECT  $state        User state
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function addMemberToGroup($groupId, JUser $groupmember, $state = 1)
	{
		JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_easysocial/tables');

		$db = JFactory::getDBO();

		$table = JTable::getInstance('clusternode', 'SocialTable', array('dbo', $db));
		$table->load(array('cluster_id' => (int) $groupId, 'uid' => (int) $groupmember->id));
		$state = $state == 1 ? SOCIAL_STATE_PUBLISHED : SOCIAL_STATE_UNPUBLISHED;

		$table->cluster_id = $groupId;
		$table->uid        = $groupmember->id;
		$table->state      = $state;
		$table->type       = SOCIAL_TYPE_USER;

		if (!$table->store())
		{
			return false;
		}

		return true;
	}
}
