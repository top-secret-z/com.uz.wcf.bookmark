<?php
namespace wcf\system\user\notification\object\type;
use wcf\data\bookmark\share\BookmarkShare;
use wcf\data\bookmark\share\BookmarkShareList;
use wcf\system\user\notification\object\BookmarkUserNotificationObject;
use wcf\system\WCF;

/**
 * Represents a bookmark notification object type.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkUserNotificationObjectType extends AbstractUserNotificationObjectType {
	/**
	 * @inheritDoc
	 */
	protected static $decoratorClassName = BookmarkUserNotificationObject::class;
	
	/**
	 * @inheritDoc
	 */
	protected static $objectClassName = BookmarkShare::class;
	
	/**
	 * @inheritDoc
	 */
	protected static $objectListClassName = BookmarkShareList::class;
}