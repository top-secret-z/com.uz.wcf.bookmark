<?php
namespace wcf\page;
use wcf\data\bookmark\share\ExtendedBookmarkShareList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * Shows the bookmarks shared with this user.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkSharedListPage extends MultipleLinkPage {
	/**
	 * @inheritDoc
	 */
	public $loginRequired = true;
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.bookmark.canUseBookmark'];
	
	/**
	 * @inheritDoc
	 */
	public $sqlOrderBy = 'time DESC';
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		$this->objectList = new ExtendedBookmarkShareList();
		$this->objectList->getConditionBuilder()->add("bookmark_share.receiverID = ?", [WCF::getUser()->userID]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function show() {
		
		// set active tab
		UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.community.bookmarkShared');
		
		parent::show();
	}
}
