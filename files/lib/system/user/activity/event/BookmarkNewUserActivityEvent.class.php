<?php
namespace wcf\system\user\activity\event;
use wcf\data\bookmark\BookmarkList;
use wcf\system\user\activity\event\IUserActivityEvent;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * User activity event implementation for new bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkNewUserActivityEvent extends SingletonFactory implements IUserActivityEvent {
	/**
	 * @inheritDoc
	 */
	public function prepare(array $events) {
		$objectIDs = [];
		foreach ($events as $event) {
			$objectIDs[] = $event->objectID;
		}
		
		// fetch bookmarks
		$bookmarkList = new BookmarkList();
		$bookmarkList->setObjectIDs($objectIDs);
		$bookmarkList->readObjects();
		$bookmarks = $bookmarkList->getObjects();
		
		// set message
		foreach ($events as $event) {
			if (isset($bookmarks[$event->objectID])) {
				$bookmark = $bookmarks[$event->objectID];
				
				// validate permissions
				if (!BOOKMARK_BASIC_ACTIVITY_ENABLE || !$bookmark->canSee()) {
					continue;
				}
				
				$event->setIsAccessible();
				
				// title
				$text = WCF::getLanguage()->getDynamicVariable('wcf.bookmark.recentActivityEvent.new', ['bookmark' => $bookmarks[$event->objectID]]);
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
