<?php
namespace wcf\system\bulk\processing\user;
use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\cache\builder\BookmarkNewestBoxCacheBuilder;
use wcf\system\cache\builder\BookmarkTopBoxCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Bulk processing action implementation for deleting bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkDeleteBulkProcessingAction extends AbstractUserBulkProcessingAction {
	/**
	 * @inheritDoc
	 */
	public function executeAction(DatabaseObjectList $objectList) {
		if (!($objectList instanceof UserList)) return;
		
		$userIDs = $objectList->getObjectIDs();
		
		if (!empty($userIDs)) {
			// delete bookmarks; will delete shares automatically
			$conditions = new PreparedStatementConditionBuilder();
			$conditions->add("userID IN (?)", [$userIDs]);
			
			// update users
			$sql = "UPDATE	wcf".WCF_N."_user
					SET bookmarks = 0, bookmarkShares = 0
				".$conditions;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditions->getParameters());
			
			// delete bookmarks
			$conditions = new PreparedStatementConditionBuilder();
			$conditions->add("userID IN (?)", [$userIDs]);
			$sql = "DELETE FROM	wcf".WCF_N."_bookmark
				".$conditions;
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute($conditions->getParameters());
			
			// reset caches
			BookmarkNewestBoxCacheBuilder::getInstance()->reset();
			BookmarkTopBoxCacheBuilder::getInstance()->reset();
			
			// reset user storage
			UserStorageHandler::getInstance()->reset($userIDs, 'unreadBookmarkCount');
		}
	}
	
	/**
	 * @inheritDoc
	 */
	public function getObjectList() {
		$userList = parent::getObjectList();
		
		// only users with bookmarks
		$userList->getConditionBuilder()->add("(user_table.bookmarks > 0 OR user_table.bookmarkShares > 0)");
		
		return $userList;
	}
}
