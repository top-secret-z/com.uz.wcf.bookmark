<?php
namespace wcf\system\box;
use wcf\data\DatabaseObject;
use wcf\system\cache\builder\BookmarkTopBoxCacheBuilder;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows users with most shared bookmarks.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkTopBoxController extends AbstractDatabaseObjectListBoxController {
	/**
	 * @inheritDoc
	 */
	protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];
	
	/**
	 * @inheritDoc
	 */
	public $defaultLimit = 5;
	public $maximumLimit = 25;
	public $minimumLimit = 1;
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = [];
	
	/**
	 * @inheritDoc
	 */
	protected $sortFieldLanguageItemPrefix = '';
	
	/**
	 * users loaded from cache
	 */
	public $users = [];
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		if (WCF::getUser()->userID) {
			return LinkHandler::getInstance()->getLink('User', ['object' => WCF::getUser()], '#bookmark');
		}
		return '';
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasLink() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getObjectList() {
		return null;
	}
	
	/**
	 * @inheritDoc
	 */
	public function hasContent() {
		if (!MODULE_BOOKMARK || !BOOKMARK_SHARE_ENABLE || !WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
			return false;
		}
		
		parent::hasContent();
		
		return count($this->users) > 0;
	}
	
	/**
	 * @inheritDoc
	 */
	protected function loadContent() {
		$this->readObjects();
		
		$this->content = $this->getTemplate();
	}
	
	/**
	 * @inheritDoc
	 */
	protected function readObjects() {
		EventHandler::getInstance()->fireAction($this, 'readObjects');
		
		$userIDs = BookmarkTopBoxCacheBuilder::getInstance()->getData(['limit' => $this->limit]);
		
		if (!empty($userIDs)) {
			$this->users = UserProfileRuntimeCache::getInstance()->getObjects($userIDs);
			
			// sort users
			if (!empty($this->users)) {
				DatabaseObject::sort($this->users, 'bookmarkShares', 'DESC');
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getTemplate() {
		return WCF::getTPL()->fetch('boxBookmarkTop', 'wcf', [
				'users' => $this->users
		]);
	}
}
