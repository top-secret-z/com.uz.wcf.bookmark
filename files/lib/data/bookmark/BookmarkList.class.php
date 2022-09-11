<?php
namespace wcf\data\bookmark;
use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents a list of bookmarks.
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkList extends DatabaseObjectList {
	/**
	 * @inheritDoc
	 */
	public $className = Bookmark::class;
	
	/**
	 * Returns timestamp of oldest bookmark fetched.
	 */
	public function getLastBookmarkTime() {
		$lastBookmarkTime = 0;
		foreach ($this->objects as $bookmark) {
			if (!$lastBookmarkTime) {
				$lastBookmarkTime = $bookmark->time;
			}
			
			$lastBookmarkTime = min($lastBookmarkTime, $bookmark->time);
		}
		
		return $lastBookmarkTime;
	}
	
	/**
	 * Returns a list of available bookmark types.
	 */
	public function getAvailableTypes() {
		$types = [];
		$sql = "SELECT	DISTINCT bookmark.type
				FROM	wcf".WCF_N."_bookmark bookmark";
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute();
		while ($row = $statement->fetchArray()) {
			if ($row['type']) {
				$types[$row['type']] = WCF::getLanguage()->get('wcf.bookmark.type.' . $row['type']);
			}
		}
		
		ksort($types);
		
		return $types;
	}
}
