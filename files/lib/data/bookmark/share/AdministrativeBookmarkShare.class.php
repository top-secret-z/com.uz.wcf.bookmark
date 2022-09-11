<?php 
namespace wcf\data\bookmark\share;
use wcf\data\bookmark\Bookmark;
use wcf\data\DatabaseObjectDecorator;
use wcf\system\WCF;

/**
 * Represents aa administrative bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class AdministrativeBookmarkShare extends DatabaseObjectDecorator {
	/**
	 * @inheritDoc
	 */
	public static $baseClass = BookmarkShare::class;
}
