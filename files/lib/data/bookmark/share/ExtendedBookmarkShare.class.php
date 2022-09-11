<?php 
namespace wcf\data\bookmark\share;
use wcf\data\bookmark\Bookmark;
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Represents a bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class ExtendedBookmarkShare extends DatabaseObjectDecorator {
	/**
	 * @inheritDoc
	 */
	protected static $baseClass = BookmarkShare::class;
	
	/**
	 * user profile object
	 */
	protected $sharerProfile;
	
	/**
	 * Returns bookmark share title.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Returns bookmark share url.
	 */
	public function getUrl() {
		$bookmark = new Bookmark($this->bookmarkID);
		return $this->url;
	}
	
	/**
	 * Returns the user profile object.
	 */
	public function getSharerProfile() {
		if ($this->sharerProfile === null) {
			if ($this->sharerID) {
				$this->sharerProfile = UserProfileRuntimeCache::getInstance()->getObject($this->sharerID);
			}
			else {
				$this->sharerProfile = UserProfile::getGuestUserProfile($this->sharerName);
			}
		}
		return $this->sharerProfile;
	}
}
