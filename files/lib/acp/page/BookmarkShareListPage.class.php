<?php
namespace wcf\acp\page;
use wcf\data\bookmark\share\AdministrativeBookmarkShareList;
use wcf\data\user\User;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows all bookmark shares.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShareListPage extends SortablePage {
	/**
	 * @inheritDoc
	 */
	public $activeMenuItem = 'wcf.acp.menu.link.bookmarkShare.list';
	
	/**
	 * @inheritDoc
	 */
	public $neededPermissions = ['admin.user.canEditUser'];
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortField = 'time';
	
	/**
	 * @inheritDoc
	 */
	public $defaultSortOrder = 'DESC';
	
	/**
	 * @inheritDoc
	 */
	public $validSortFields = ['shareID', 'username', 'receiverName', 'shareWith', 'url', 'title', 'remark'];
	
	/**
	 * filter data
	 */
	public $username = '';
	
	/**
	 * @inheritDoc
	 */
	public $objectListClassName = AdministrativeBookmarkShareList::class;
	
	/**
	 * @inheritDoc
	 */
	public function readParameters() {
		parent::readParameters();
		
		if (!empty($_REQUEST['username'])) $this->username = StringUtil::trim($_REQUEST['username']);
	}
	
	/**
	 * @inheritDoc
	 */
	protected function initObjectList() {
		parent::initObjectList();
		
		// apply filter
		if (!empty($this->username)) {
			$user = User::getUserByUsername($this->username);
			if ($user->userID) {
				$this->objectList->getConditionBuilder()->add('bookmark_share.userID = ?', [$user->userID]);
			}
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function assignVariables() {
		parent::assignVariables();
		
		WCF::getTPL()->assign([
				'username' => $this->username
		]);
	}
}
