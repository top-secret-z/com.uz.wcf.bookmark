<?php
namespace wcf\system\user\notification\object;
use wcf\data\bookmark\share\BookmarkShare;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\bookmark\Bookmark;
use wcf\system\WCF;

/**
 * Notification object for bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkAcceptUserNotificationObject extends DatabaseObjectDecorator implements IUserNotificationObject {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = BookmarkShare::class;
	
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->remark;
	}
	
	/**
	 * @inheritDoc
	 */
	public function getURL() {
		return $this->getDecoratedObject()->getUrl();
	}
	
	/**
	 * @inheritDoc
	 */
	public function getAuthorID() {
		return WCF::getUser()->userID;
	}
}
