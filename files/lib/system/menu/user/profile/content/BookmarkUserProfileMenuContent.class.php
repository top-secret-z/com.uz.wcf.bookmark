<?php
namespace wcf\system\menu\user\profile\content;
use wcf\data\bookmark\BookmarkList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles user profile bookmark content.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent {
	/**
	 * @inheritDoc
	 */
	public function getContent($userID) {
		$bookmarkList = new BookmarkList();
		
		$bookmarkList->sqlLimit = 15;
		
		$bookmarkList->getConditionBuilder()->add("userID = ?", [$userID]);
		
		if (WCF::getUser()->userID != $userID) {
			// follower?
			if (WCF::getUserProfileHandler()->isFollowing($userID)) {
				$bookmarkList->getConditionBuilder()->add("(isPrivate = ? OR isPrivate = ?)", [0, 2]);
			}
			else $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [0]);
		}
		$bookmarkList->sqlOrderBy = "time DESC";
		$bookmarkList->readObjects();
		
		WCF::getTPL()->assign([
				'bookmarkList' => $bookmarkList,
				'userID' => $userID,
				'lastBookmarkTime' => $bookmarkList->getLastBookmarkTime(),
				'shareEnable' => BOOKMARK_SHARE_ENABLE
		]);
		
		return WCF::getTPL()->fetch('userProfileBookmark');
	}
	
	/**
	 * @inheritDoc
	 */
	public function isVisible($userID) {
		if (WCF::getUser()->userID == $userID) return true;
		if (WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) return true;
		return false;
	}
}
