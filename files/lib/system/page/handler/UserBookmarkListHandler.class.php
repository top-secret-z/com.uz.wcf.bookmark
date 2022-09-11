<?php
namespace wcf\system\page\handler;
use wcf\system\page\handler\AbstractMenuPageHandler;
use wcf\system\WCF;

/**
 * Page handler for Bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class UserBookmarkListHandler extends AbstractMenuPageHandler {
	/**
	 * @inheritDoc
	 */
	public function getOutstandingItemCount($objectID = null) {
		return 0;
	}
	
	/**
	 * @inheritDoc
	 */
	public function isVisible($objectID = null) {
		if (!MODULE_BOOKMARK) return false;
		
		return WCF::getSession()->getPermission('user.bookmark.canUseBookmark') ? true : false;
	}
}
