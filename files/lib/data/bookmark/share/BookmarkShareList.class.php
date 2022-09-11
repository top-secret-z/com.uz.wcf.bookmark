<?php
namespace wcf\data\bookmark\share;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents a list of bookmark shares.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShareList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = BookmarkShare::class;
}
