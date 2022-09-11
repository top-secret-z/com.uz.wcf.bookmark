<?php 
namespace wcf\data\bookmark\share;
use wcf\data\bookmark\Bookmark;
use wcf\data\DatabaseObject;
use wcf\data\user\User;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkShare extends DatabaseObject {
	/**
	 * list of point of times for each period's end
	 */
	protected static $periods = [];
	
	/**
	 * user profile object
	 */
	protected $userProfile;
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableName = 'bookmark_share';
	
	/**
	 * @inheritDoc
	 */
	protected static $databaseTableIndexName = 'shareID';
	
	/**
	 * true if the active user has shares
	 */
	protected static $hasShares;
	
	/**
	 * Returns bookmark share title.
	 */
	public function getTitle() {
		return $this->notice;
	}
	
	/**
	 * Returns bookmark share url.
	 */
	public function getUrl() {
		$bookmark = new Bookmark($this->bookmarkID);
		return $bookmark->getUrl();
	}
	
	/**
	 * Returns the bookmark.
	 */
	public function getBookmark() {
		$bookmark = new Bookmark($this->bookmarkID);
		if (!$bookmark->bookmarkID) return null;
		return $bookmark;
	}
	
	/**
	 * Get user's shares for a specific bookmark
	 */
	public static function getShares($bookmarkID) {
		$shareList = new BookmarkShareList();
		$shareList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
		$shareList->getConditionBuilder()->add('refused = ?', [0]);
		$shareList->getConditionBuilder()->add('accepted = ?', [1]);
		$shareList->getConditionBuilder()->add('bookmarkID = ?', [$bookmarkID]);
		$shareList->readObjects();
		$shares = $shareList->getObjects();
		
		if (!count($shares)) return '';
		
		$temp = [];
		foreach ($shares as $share) {
			$user = UserProfileRuntimeCache::getInstance()->getObject($share->receiverID);
			if ($user->userID) {
				$temp[] = "<a href=" . $user->getLink() . ' class="userLink" data-user-id="' . $user->userID . '">' . $user->username . '</a>';
			}
		}
		
		if (count($temp)) return implode(', ', $temp);
		return '';
	}
	
	/**
	 * Returns the readable period matching this bookmark share
	 * copied from 
	 * Provides a default implementation for user notification events.
	 * 
	 * @author	Joshua Ruesweg, Marcel Werk, Oliver Kliebisch
 	 * @copyright	2001-2016 WoltLab GmbH, Oliver Kliebisch.
	 *
	 */
	public function getPeriod() {
		if (empty(self::$periods)) {
			$date = DateUtil::getDateTimeByTimestamp(TIME_NOW);
			$date->setTimezone(WCF::getUser()->getTimeZone());
			$date->setTime(0, 0, 0);
			
			self::$periods[$date->getTimestamp()] = WCF::getLanguage()->get('wcf.date.period.today');
			
			// 1 day back
			$date->modify('-1 day');
			self::$periods[$date->getTimestamp()] = WCF::getLanguage()->get('wcf.date.period.yesterday');
			
			// 2-6 days back
			for ($i = 0; $i < 6; $i++) {
				$date->modify('-1 day');
				self::$periods[$date->getTimestamp()] = DateUtil::format($date, 'l');
			}
		}
		
		foreach (self::$periods as $time => $period) {
			if ($this->time >= $time) {
				return $period;
			}
		}
		
		return WCF::getLanguage()->get('wcf.date.period.older');
	}
	
	/**
	 * Returns user profile matching this bookmark share.
	 */
	public function getUserProfile() {
		if ($this->userProfile === null) {
			$this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
		}
		
		return $this->userProfile;
	}
	
	/**
	 * Check whether user has shares
	 */
	public static function hasShares() {
		if (self::$hasShares === null) {
			$sql = "SELECT	COUNT(*) as count
					FROM	wcf".WCF_N."_bookmark_share
					WHERE	receiverID = ?";
			$statement = WCF::getDB()->prepareStatement($sql);
			$statement->execute([WCF::getUser()->userID]); 
			$row = $statement->fetchArray();
						self::$hasShares = ($row['count'] ? true : false);
		}
		
		return self::$hasShares;
	}
}
