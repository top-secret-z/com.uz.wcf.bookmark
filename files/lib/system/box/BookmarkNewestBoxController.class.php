<?php
namespace wcf\system\box;
use wcf\system\cache\builder\BookmarkNewestBoxCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows newest bookmarks.
 *
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkNewestBoxController extends AbstractDatabaseObjectListBoxController {
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
	 * cached bookmarks
	 */
	protected $bookmarks = [];
	
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
		if (!MODULE_BOOKMARK || !WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
			return false;
		}
		
		parent::hasContent();
		
		return count($this->bookmarks) > 0;
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
		
		$this->bookmarks = BookmarkNewestBoxCacheBuilder::getInstance()->getData(['limit' => $this->limit]);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function getTemplate() {
		return WCF::getTPL()->fetch('boxBookmarkNewest', 'wcf', [
				'bookmarks' => $this->bookmarks
		]);
	}
}
