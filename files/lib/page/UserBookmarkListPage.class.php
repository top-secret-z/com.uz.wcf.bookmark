<?php
namespace wcf\page;
use wcf\data\bookmark\BookmarkList;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the bookmarks of this user.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class UserBookmarkListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'com.uz.wcf.bookmark.List';
	
	/**
	 * @inheritDoc
	 */
	public $neededModules = ['MODULE_BOOKMARK'];
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['user.bookmark.canUseBookmark'];
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = BookmarkList::class;
	
	/**
	 * @inheritDoc
	 */
	public $itemsPerPage = 15;
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'time';
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['time', 'type', 'title', 'remark'];
	
	/**
	 * filter
	 */
	public $search = '';
	public $type = '';
	public $availableTypes = [];
	public $isPrivate = '';
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_REQUEST['search'])) $this->search = StringUtil::trim($_REQUEST['search']);
		if (!empty($_REQUEST['isPrivate'])) $this->isPrivate = $_REQUEST['isPrivate'];
		if (!empty($_REQUEST['type'])) $this->type = $_REQUEST['type'];
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		// user
		$this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
		
		// get data
		$this->availableTypes = $this->objectList->getAvailableTypes();
		
		// filter
		if (!empty($this->search)) {
			$search = '%'.$this->search.'%';
			$this->objectList->getConditionBuilder()->add('title LIKE ? OR remark LIKE ?', [$search, $search]);
		}
		
		if (!empty($this->isPrivate)) {
			$this->objectList->getConditionBuilder()->add('isPrivate = ?', [$this->isPrivate]);
		}
		
		if (!empty($this->type)) {
			$this->objectList->getConditionBuilder()->add('type = ?', [$this->type]);
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'search' => $this->search,
				'isPrivate' => $this->isPrivate,
				'type' => $this->type,
				'availableTypes' => $this->availableTypes,
				'shareEnable' => BOOKMARK_SHARE_ENABLE
		]);
	}
}
