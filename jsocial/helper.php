<?php
/**
 * @version     SVN: <svn_id>
 * @package     Techjoomla.Libraries
 * @subpackage  JSocial
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Helper class for common functions in JSocial library
 *
 * @package     Techjoomla.Libraries
 * @subpackage  JSocial
 * @since       1.0.4
 */
class JSocialHelper
{
	/**
	 * Get itemid for given link
	 *
	 * @param   string   $link          link
	 * @param   integer  $skipIfNoMenu  Decide to use Itemid from $input
	 *
	 * @return  item id
	 *
	 * @since  3.0
	 */
	public static function getItemId($link, $skipIfNoMenu = 0)
	{
		$itemid    = 0;
		$mainframe = JFactory::getApplication();
		$input     = JFactory::getApplication()->input;

		if ($mainframe->issite())
		{
			$JSite = new JSite;
			$menu  = $JSite->getMenu();
			$items = $menu->getItems('link', $link);

			if (isset($items[0]))
			{
				$itemid = $items[0]->id;
			}
		}

		if (!$itemid)
		{
			$db = JFactory::getDbo();

			if (JVERSION >= '3.0')
			{
				$query = "SELECT id FROM #__menu
				 WHERE link LIKE '%" . $link . "%'
				 AND published =1
				 LIMIT 1";
			}
			else
			{
				$query = "SELECT id FROM " . $db->quoteName('#__menu') . "
				 WHERE link LIKE '%" . $link . "%'
				 AND published =1
				 ORDER BY ordering
				 LIMIT 1";
			}

			$db->setQuery($query);
			$itemid = $db->loadResult();
		}

		if (!$itemid)
		{
			if ($skipIfNoMenu)
			{
				$itemid = 0;
			}
			else
			{
				$itemid  = $input->get->get('Itemid', '0', 'INT');
			}
		}

		return $itemid;
	}
}
