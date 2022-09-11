<?php
namespace wcf\system\user\activity\event;
use wcf\data\user\UserProfileList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for shared bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShareUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @inheritDoc
	 */
	public function prepare(array $events) {
		$objectIDs = [];
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// fetch user profiles
		$userList = new UserProfileList();
		$userList->getConditionBuilder()->add("user_table.userID IN (?)", [$objectIDs]);
		$userList->readObjects();
		$users = $userList->getObjects();
		
		// set message
		foreach ($events as $event) {
			if (isset($users[$event->objectID])) {
				if (!$users[$event->objectID]->getPermission('user.bookmark.canUseBookmark')) {
					continue;
				}
				
				if (!BOOKMARK_ACTIVITY_ENABLE || !WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
					continue;
				}
				
				$event->setIsAccessible();
				
				// title
				$text = WCF::getLanguage()->getDynamicVariable('wcf.bookmark.recentActivityEvent.share', ['user' => $users[$event->objectID]]);
				$event->setTitle($text);
				
				// description
				$event->setDescription('');
			}
			else {
				$event->setIsOrphaned();
			}
		}
	}
}
