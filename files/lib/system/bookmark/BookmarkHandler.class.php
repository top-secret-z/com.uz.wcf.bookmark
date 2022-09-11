<?php
namespace wcf\system\bookmark;
use wcf\data\user\User;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\SingletonFactory;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Handles bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkHandler extends SingletonFactory {
	/**
	 * number of unread bookmark shares
	 */
	protected $unreadBookmarkCount = [];
	
	/**
	 * number of total bookmark shares to user
	 */
	protected $totalBookmarkShares = [];
	
	/**
	 * Returns the number of unread bookmark shares for given user.
	 */
	public function getUnreadBookmarkCount($userID = null, $skipCache = false) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		// skip if shares are disabled
		if (!BOOKMARK_SHARE_ENABLE) return 0;
		
		if (!isset($this->unreadBookmarkCount[$userID]) || $skipCache) {
			$this->unreadBookmarkCount[$userID] = 0;
			
			// load storage data
			UserStorageHandler::getInstance()->loadStorage([$userID]);
			$data = UserStorageHandler::getInstance()->getStorage([$userID], 'unreadBookmarkCount');
			
			// cache does not exist or is outdated
			if ($data[$userID] === null || $skipCache) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('receiverID = ?', [$userID]);
				$conditionBuilder->add('lastVisitTime < time');
				
				$sql = "SELECT	COUNT(*) AS count
						FROM	wcf".WCF_N."_bookmark_share
						".$conditionBuilder->__toString();
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				$row = $statement->fetchArray();
				$this->unreadBookmarkCount[$userID] = $row['count'];
				
				// update storage data
				UserStorageHandler::getInstance()->update($userID, 'unreadBookmarkCount', serialize($this->unreadBookmarkCount[$userID]));
			}
			else {
				$this->unreadBookmarkCount[$userID] = unserialize($data[$userID]);
			}
		}
		
		return $this->unreadBookmarkCount[$userID];
	}
	
	/**
	 * Returns the total number of bookmark shares for given user.
	 */
	public function getTotalBookmarkShares($userID = null, $skipCache = false) {
		if ($userID === null) $userID = WCF::getUser()->userID;
		
		if (!isset($this->totalBookmarkShares[$userID]) || $skipCache) {
			$this->totalBookmarkShares[$userID] = 0;
			
			// load storage data
			UserStorageHandler::getInstance()->loadStorage([$userID]);
			$data = UserStorageHandler::getInstance()->getStorage([$userID], 'totalBookmarkShares');
			
			// cache does not exist or is outdated
			if ($data[$userID] === null || $skipCache) {
				$conditionBuilder = new PreparedStatementConditionBuilder();
				$conditionBuilder->add('receiverID = ?', [$userID]);
				
				$sql = "SELECT	COUNT(*) AS count
						FROM	wcf".WCF_N."_bookmark_share
						".$conditionBuilder->__toString();
				$statement = WCF::getDB()->prepareStatement($sql);
				$statement->execute($conditionBuilder->getParameters());
				$row = $statement->fetchArray();
				$this->totalBookmarkShares[$userID] = $row['count'];
				
				// update storage data
				UserStorageHandler::getInstance()->update($userID, 'totalBookmarkShares', serialize($this->totalBookmarkShares[$userID]));
			}
			else {
				$this->totalBookmarkShares[$userID] = unserialize($data[$userID]);
			}
		}
		
		return $this->totalBookmarkShares[$userID];
	}
}
