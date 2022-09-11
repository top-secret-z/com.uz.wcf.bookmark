<?php
namespace wcf\data\bookmark;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit bookmarks.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = Bookmark::class;
}
