<?php
namespace wcf\data\bookmark\share;
use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\bookmark\Bookmark;
use wcf\data\bookmark\BookmarkEditor;
use wcf\data\bookmark\BookmarkList;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\system\bookmark\BookmarkHandler;
use wcf\system\cache\builder\BookmarkTopBoxCacheBuilder;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\exception\UserInputException;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Executes bookmark-related actions.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShareAction extends AbstractDatabaseObjectAction {
	/**
	 * @inheritDoc
	 */
	protected $className = BookmarkShareEditor::class;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsDelete = ['admin.user.canEditUser'];
	
	/**
	 * data
	 */
	public $share;
	
	/**
	 * @inheritDoc
	 */
	protected $permissionsUpdate = ['user.bookmark.canUseBookmark'];
	
	/**
	 * Validates the delete action
	 */
	public function validateDelete() {
		// read objects
		if (empty($this->objects)) {
			$this->readObjects();
			
			if (empty($this->objects)) {
				throw new UserInputException('objectIDs');
			}
		}
		
		if (!WCF::getSession()->getPermission('admin.user.canEditUser')) {
			throw new PermissionDeniedException();
		}
	}
	
	/**
	 * Deletes a bookmark share.
	 */
	public function delete() {
		$affectedUsers = [];
		foreach ($this->getObjects() as $object) {
			$bookmark = new Bookmark($object->bookmarkID);
			if (!$bookmark->bookmarkID) continue;
			
			// correct bookmark's shareWith
			$newShareWith = [];
			$shareWith = explode(', ', $bookmark->shareWith);
			foreach ($shareWith as $with) {
				if ($with != $object->receiverName && $with != '<del>'.$object->receiverName.'</del>') {
					$newShareWith[] = $with;
				}
			}
			$bookmarkEditor = new BookmarkEditor($bookmark);
			$bookmarkEditor->update(['shareWith' => implode(', ', $newShareWith)]);
			
			// update user share count
			$user = new User($object->userID);
			if ($user->userID && $user->bookmarkShares > 0) {
				$userEditor = new UserEditor($user);
				$userEditor->updateCounters(['bookmarkShares' => -1]);
			}
			
			$affectedUsers[] = $object->receiverID;
		}
		
		// reset storage and cache
		if (count($affectedUsers)) UserStorageHandler::getInstance()->reset($affectedUsers, 'unreadBookmarkCount');
		BookmarkTopBoxCacheBuilder::getInstance()->reset();
		
		parent::delete();
	}
	
	/**
	 * Validates parameters to return the mixed bookmark list.
	 */
	public function validateGetMixedShareList() {
		// does nothing
	}
	
	/**
	 * Returns a mixed share list with up to 10 items.
	 */
	public function getMixedShareList() {
		if (BOOKMARK_SHARE_ENABLE) {
			$shareList = new BookmarkShareList();
			$shareList->getConditionBuilder()->add('bookmark_share.receiverID = ?', [WCF::getUser()->userID]);
			$shareList->sqlLimit = 10;
			$shareList->sqlOrderBy = 'time DESC';
			$shareList->readObjects();
			
			UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'unreadBookmarkCount');
		}
		else {
			$shareList = new BookmarkList();
			$shareList->getConditionBuilder()->add('bookmark.userID = ?', [WCF::getUser()->userID]);
			$shareList->sqlLimit = 10;
			$shareList->sqlOrderBy = 'time DESC';
			$shareList->readObjects();
		}
		
		WCF::getTPL()->assign([
				'shares' => $shareList->getObjects()
		]);
		
		return [
				'template' => WCF::getTPL()->fetch('bookmarkListUserPanel')
		];
	}
	
	/**
	 * Validates parameters for markAsRead action.
	 */
	public function validateMarkAsRead() {
		if (isset($this->parameters['visitTime'])) {
			$this->parameters['visitTime'] = intval($this->parameters['visitTime']);
			if ($this->parameters['visitTime'] > TIME_NOW) {
				$this->parameters['visitTime'] = TIME_NOW;
			}
		}
	}
	
	/**
	 * Executes the markAsRead action.
	 */
	public function markAsRead() {
		if (empty($this->parameters['visitTime'])) {
			$this->parameters['visitTime'] = TIME_NOW;
		}
		
		if (empty($this->objects)) {
			$this->readObjects();
		}
		
		$shareIDs = [];
		$sql = "UPDATE	wcf".WCF_N."_bookmark_share
				SET		lastVisitTime = ?
				WHERE	shareID = ?";
		$statement = WCF::getDB()->prepareStatement($sql);
		WCF::getDB()->beginTransaction();
		foreach ($this->getObjects() as $share) {
			$statement->execute([
					$this->parameters['visitTime'],
					$share->shareID
			]);
			$shareIDs[] = $share->shareID;
		}
		WCF::getDB()->commitTransaction();
		
		// reset storage
		UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'unreadBookmarkCount');
		
		$returnValues = [
				'totalCount' => BookmarkHandler::getInstance()->getUnreadBookmarkCount(null, true)
		];
		
		if (count($shareIDs) == 1) {
			$returnValues['markAsRead'] = reset($shareIDs);
		}
		
		return $returnValues;
	}
	
	/**
	 * Validates parameters for markAllAsRead action.
	 */
	public function validateMarkAllAsRead() {
		// does nothing
	}
	
	/**
	 * Executes the markAllAsRead action.
	 */
	public function markAllAsRead() {
		if (WCF::getUser()->userID) {
			$sql = "UPDATE	wcf".WCF_N."_bookmark_share
					SET		lastVisitTime = ?
					WHERE	receiverID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([TIME_NOW, WCF::getUser()->userID]);
			
			UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'unreadBookmarkCount');
		}
		
		$returnValues['markAllAsRead'] = true;
		
		return $returnValues;
	}
}
