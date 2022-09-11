<?php
namespace wcf\system\user\notification\event;
use wcf\data\bookmark\Bookmark;
use wcf\data\bookmark\share\BookmarkShare;

/**
 * User notification event for bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkUserNotificationEvent extends AbstractUserNotificationEvent {
	/**
	 * @inheritDoc
	 */
	public function getTitle() {
		return $this->getLanguage()->get('wcf.user.notification.bookmark.title');
	}
	
	/**
	 * @inheritDoc
	 */
	public function getMessage() {
		$bookmark = $this->getUserNotificationObject()->getBookmark();
		$share = new BookmarkShare($this->additionalData['shareID']);
		if (!$share->shareID) $remark = '';
		$remark = $share->remark;
		
		return $this->getLanguage()->getDynamicVariable('wcf.user.notification.bookmark.message', [
				'author' => $this->author,
				'url' => $bookmark->url,
				'title' => $bookmark->title,
				'remark' => $remark
		]);
	}
	
	/**
	 * @inheritDoc
	 */
	public function getEmailMessage($notificationType = 'instant') {
		return [
			'message-id' => 'wcf.user.notification.bookmark/'.$this->getUserNotificationObject()->shareID,
			'template' => 'email_notification_bookmark',
			'application' => 'wcf'
		];
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
}
