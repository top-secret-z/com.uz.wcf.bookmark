<?php
namespace wcf\data\bookmark\share;
use wcf\data\DatabaseObjectEditor;

/**
 * Provides functions to edit bookmark shares.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShareEditor extends DatabaseObjectEditor {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = BookmarkShare::class;
}
