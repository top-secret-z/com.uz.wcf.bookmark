<?php
namespace wcf\system\user\notification\event;
use wcf\data\bookmark\Bookmark;
use wcf\data\bookmark\share\BookmarkShare;
use wcf\data\user\User;
use wcf\system\request\LinkHandler;

/**
 * User notification event for bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkRefuseUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.bookmark.refuse.title');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		$bookmark = $this->getUserNotificationObject()->getBookmark();
		$share = new BookmarkShare($this->additionalData['shareID']);
		$user = new User($share->userID);
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.bookmark.refuse.message', [
				'author' => $this->author,
				'url' => LinkHandler::getInstance()->getLink('User', ['object' => $user], '#bookmark'),
				'title' => $bookmark->title,
				'remark' => $share->remark
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		throw new \LogicException('Unreachable');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getLink() {
		return $this->userNotificationObject->getUrl();
	}
	
	/**
	 * @inheritDoc
	 */
	public function checkAccess() {
		return true;
	}
	
	/**
	 * @inheritDoc
	 */
	public function supportsEmailNotification() {
		return false;
	}
}
