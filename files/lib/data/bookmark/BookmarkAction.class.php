<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace wcf\data\bookmark;

use wcf\data\AbstractDatabaseObjectAction;
use wcf\data\bookmark\share\BookmarkShare;
use wcf\data\bookmark\share\BookmarkShareAction;
use wcf\data\bookmark\share\BookmarkShareEditor;
use wcf\data\user\User;
use wcf\data\user\UserEditor;
use wcf\data\user\UserProfile;
use wcf\system\application\ApplicationHandler;
use wcf\system\cache\builder\BookmarkNewestBoxCacheBuilder;
use wcf\system\cache\builder\BookmarkTopBoxCacheBuilder;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\user\activity\event\UserActivityEventHandler;
use wcf\system\user\notification\object\BookmarkAcceptUserNotificationObject;
use wcf\system\user\notification\object\BookmarkRefuseUserNotificationObject;
use wcf\system\user\notification\object\BookmarkUserNotificationObject;
use wcf\system\user\notification\UserNotificationHandler;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;
use wcf\util\ArrayUtil;

/**
 * Executes bookmark-related actions.
 */
class BookmarkAction extends AbstractDatabaseObjectAction
{
    /**
     * @inheritDoc
     */
    protected $className = BookmarkEditor::class;

    /**
     * @inheritDoc
     */
    protected $permissionsCreate = ['user.bookmark.canUseBookmark'];

    /**
     * @inheritDoc
     */
    protected $permissionsDelete = ['user.bookmark.canUseBookmark'];

    /**
     * @inheritDoc
     */
    protected $permissionsUpdate = ['user.bookmark.canUseBookmark'];

    /**
     * @inheritDoc
     */
    protected $allowGuestAccess = ['load', 'viewBookmark'];

    /**
     * data
     */
    public $bookmark;

    public $bookmarkShare;

    public $alreadyBookmarked = 0;

    /**
     * Validates parameters to display the 'add bookmark' form.
     */
    public function validateGetAddBookmarkDialog()
    {
        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);

        if (isset($this->parameters['type']) && isset($this->parameters['objectID'])) {
            if (Bookmark::checkExist($this->parameters['type'], $this->parameters['objectID'])) {
                $this->alreadyBookmarked = 1;
            }
        }
    }

    /**
     * Gets the 'add bookmark' dialog.
     */
    public function getAddBookmarkDialog()
    {
        WCF::getTPL()->assign([
            'action' => 'add',
            'alreadyBookmarked' => $this->alreadyBookmarked,
            'isPrivate' => 0,        // = public
            'remark' => '',
            'title' => $this->parameters['title'],
            'url' => $this->parameters['url'],
        ]);

        return [
            'template' => WCF::getTPL()->fetch('bookmarkDialog'),
        ];
    }

    /**
     * Validates parameters to add bookmark.
     */
    public function validateAddBookmark()
    {
        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);

        // restrict remark and link to 64000, which is below max allowed
        $this->readString('remark', true);
        if (\mb_strlen($this->parameters['remark']) > 64000) {
            $this->parameters['remark'] = \mb_substr($this->parameters['remark'], 0, 64000);
        }
        $this->readString('url');
        if (\mb_strlen($this->parameters['url']) > 64000) {
            $this->parameters['url'] = \mb_substr($this->parameters['url'], 0, 64000);
        }
    }

    /**
     * Executes the add bookmark action.
     */
    public function addBookmark()
    {
        $user = WCF::getUser();
        $data = [
            'isExternal' => (ApplicationHandler::getInstance()->isInternalURL($this->parameters['url']) ? 0 : 1),
            'isPrivate' => $this->parameters['access'],
            'remark' => $this->parameters['remark'],
            'shareWith' => '',
            'time' => TIME_NOW,
            'title' => $this->parameters['title'],
            'type' => (empty($this->parameters['type']) ? 'external' : $this->parameters['type']),
            'objectID' => $this->parameters['objectID'] ?? 0,
            'url' => $this->parameters['url'],
            'userID' => $user->userID,
            'username' => $user->username,
        ];

        $action = new self([], 'create', ['data' => $data]);
        $bookmark = $action->executeAction()['returnValues'];

        $userEditor = new UserEditor($user);
        $userEditor->updateCounters(['bookmarks' => 1]);

        // activity
        if (BOOKMARK_BASIC_ACTIVITY_ENABLE) {
            UserActivityEventHandler::getInstance()->fireEvent('com.uz.wcf.bookmark.recentActivityEvent.new', $bookmark->bookmarkID);
        }

        // reset cache
        BookmarkNewestBoxCacheBuilder::getInstance()->reset();
    }

    /**
     * Validates parameters to display the 'add bookmark' form for edit.
     */
    public function validateGetEditBookmarkDialog()
    {
        $this->bookmark = new Bookmark($this->parameters['bookmarkID']);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);
        if ((WCF::getUser()->userID !== $this->bookmark->userID) && !WCF::getSession()->getPermission('mod.bookmark.canModerateBookmark')) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Gets the 'edit bookmark' dialog.
     */
    public function getEditBookmarkDialog()
    {
        WCF::getTPL()->assign([
            'action' => 'edit',
            'alreadyBookmarked' => 0,
            'isPrivate' => $this->bookmark->isPrivate,
            'remark' => $this->bookmark->remark,
            'title' => $this->bookmark->title,
            'url' => $this->bookmark->url,
            'shares' => BookmarkShare::getShares($this->bookmark->bookmarkID),
        ]);

        return [
            'template' => WCF::getTPL()->fetch('bookmarkDialog'),
        ];
    }

    /**
     * Validates parameters to add bookmark.
     */
    public function validateEditBookmark()
    {
        $this->bookmark = new Bookmark($this->parameters['bookmarkID']);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);
        if ((WCF::getUser()->userID !== $this->bookmark->userID) && !WCF::getSession()->getPermission('mod.bookmark.canModerateBookmark')) {
            throw new PermissionDeniedException();
        }

        // restrict remark and link to 64000, which is below max allowed
        $this->readString('remark', true);
        if (\mb_strlen($this->parameters['remark']) > 64000) {
            $this->parameters['remark'] = \mb_substr($this->parameters['remark'], 0, 64000);
        }
    }

    /**
     * Executes the add bookmark action.
     */
    public function editBookmark()
    {
        $editor = new BookmarkEditor($this->bookmark);
        $editor->update([
            'editID' => WCF::getUser()->userID,
            'editName' => WCF::getUser()->username,
            'editTime' => TIME_NOW,
            'isPrivate' => $this->parameters['access'],
            'remark' => $this->parameters['remark'],
            'title' => $this->parameters['title'],
        ]);
    }

    /**
     * Validates parameters to load bookmarks.
     */
    public function validateLoad()
    {
        $this->readInteger('lastBookmarkTime', true);
        $this->readInteger('userID');
        $this->readString('bookmarkType');
        $this->readString('bookmarkAccess');
    }

    /**
     * Loads a list of bookmarks.
     */
    public function load()
    {
        $bookmarkList = new BookmarkList();
        $bookmarkList->getConditionBuilder()->add("userID = ?", [$this->parameters['userID']]);
        if ($this->parameters['bookmarkType'] != 'all') {
            $bookmarkList->getConditionBuilder()->add("type = ?", [$this->parameters['bookmarkType']]);
        }
        if ($this->parameters['lastBookmarkTime']) {
            $bookmarkList->getConditionBuilder()->add("time < ?", [$this->parameters['lastBookmarkTime']]);
        }
        if (WCF::getUser()->userID == $this->parameters['userID']) {
            if ($this->parameters['bookmarkAccess'] == 'public') {
                $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [0]);
            }
            if ($this->parameters['bookmarkAccess'] == 'private') {
                $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [1]);
            }
            if ($this->parameters['bookmarkAccess'] == 'follower') {
                $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [2]);
            }
        } else {
            // follower?
            if (WCF::getUserProfileHandler()->isFollowing($this->parameters['userID'])) {
                $bookmarkList->getConditionBuilder()->add("(isPrivate = ? OR isPrivate = ?)", [0, 2]);
            } else {
                $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [0]);
            }
        }

        $bookmarkList->sqlLimit = 15;
        $bookmarkList->sqlOrderBy = "time DESC";
        $bookmarkList->readObjects();
        if (!\count($bookmarkList)) {
            return [];
        }

        WCF::getTPL()->assign([
            'bookmarkList' => $bookmarkList,
        ]);

        return [
            'lastBookmarkTime' => $bookmarkList->getLastBookmarkTime(),
            'template' => WCF::getTPL()->fetch('userProfileBookmarkItem'),
        ];
    }

    /**
     * Validates parameters to delete bookmarks.
     */
    public function validateDeleteBookmark()
    {
        $this->bookmark = new Bookmark($this->parameters['bookmarkID']);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);
        if ((WCF::getUser()->userID !== $this->bookmark->userID) && !WCF::getSession()->getPermission('mod.bookmark.canModerateBookmark')) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Deletes a bookmark.
     */
    public function deleteBookmark()
    {
        $action = new self([$this->bookmark], 'delete');
        $action->executeAction();

        if (WCF::getUser()->bookmarks > 0) {
            $userEditor = new UserEditor(WCF::getUser());
            $userEditor->updateCounters(['bookmarks' => -1]);
        }

        // reset cache
        BookmarkNewestBoxCacheBuilder::getInstance()->reset();
    }

    /**
     * Validates parameters to display the 'share bookmark' dialog.
     */
    public function validateGetShareBookmarkDialog()
    {
        WCF::getSession()->checkPermissions(['user.bookmark.canUseBookmark']);
    }

    /**
     * Shows the 'share bookmark' form.
     */
    public function getShareBookmarkDialog()
    {
        // exclude user himself and ignored / ignoring users
        $ignored = [];
        $sql = "SELECT    userID
                FROM    wcf" . WCF_N . "_user_ignore
                WHERE    ignoreUserID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID]);
        while ($row = $statement->fetchArray()) {
            $ignored[] = $row['userID'];
        }

        $sql = "SELECT    ignoreUserID
                FROM    wcf" . WCF_N . "_user_ignore
                WHERE    userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([WCF::getUser()->userID]);
        while ($row = $statement->fetchArray()) {
            $ignored[] = $row['ignoreUserID'];
        }

        if (\count($ignored)) {
            $users = UserProfileRuntimeCache::getInstance()->getObjects($ignored);

            foreach ($users as $user) {
                $excluded[] = $user->username;
            }
        }
        $excluded[] = WCF::getUser()->username;

        WCF::getTPL()->assign([
            'remark' => '',
        ]);

        return [
            'excludedSearchValues' => $excluded,
            'template' => WCF::getTPL()->fetch('bookmarkShareDialog'),
        ];
    }

    /**
     * Validates parameters to display the 'share bookmark' form.
     */
    public function validateShareBookmark()
    {
        $this->readStringArray('receivers');
        $this->readInteger('bookmarkID');

        $this->bookmark = new Bookmark($this->parameters['bookmarkID']);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        if ((WCF::getUser()->userID !== $this->bookmark->userID) && !WCF::getSession()->getPermission('user.bookmark.canUseBookmark')) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Shows the 'share bookmark' form.
     */
    public function shareBookmark()
    {
        // get data
        $this->bookmark = new Bookmark($this->parameters['bookmarkID']);
        $receivers = $this->parameters['receivers'];
        $receiverList = UserProfile::getUserProfilesByUsername((\is_array($receivers) ? $receivers : ArrayUtil::trim(\explode(',', $receivers))));
        $remark = $this->parameters['remark'];

        // send notification if permission
        $count = 0;
        $userIDs = $usernames = [];
        foreach ($receiverList as $user) {
            if (!$user->getPermission('user.bookmark.canUseBookmark')) {
                continue;
            }

            $count++;
            $userIDs[] = $user->userID;
            $usernames[] = $user->username;

            $data = [
                'bookmarkID' => $this->bookmark->bookmarkID,
                'receiverID' => $user->userID,
                'receiverName' => $user->username,
                'remark' => $remark = $remark,
                'time' => TIME_NOW,
                'userID' => WCF::getUser()->userID,
                'username' => WCF::getUser()->username,
            ];

            $action = new BookmarkShareAction([], 'create', ['data' => $data]);
            $returnValues = $action->executeAction();
        }

        if (\count($userIDs)) {
            UserNotificationHandler::getInstance()->fireEvent(
                'bookmark',
                'com.uz.wcf.bookmark.notification',
                new BookmarkUserNotificationObject($returnValues['returnValues']),
                $userIDs,
                ['shareID' => $returnValues['returnValues']->shareID]
            );

            // update unread bookmark shares count
            UserStorageHandler::getInstance()->reset($userIDs, 'unreadBookmarkCount');

            // update shared with
            if (!empty($this->bookmark->shareWith)) {
                $shares = \explode(', ', $this->bookmark->shareWith);
                $shares = \array_unique(\array_merge($shares, $usernames));
            } else {
                $shares = \array_unique($usernames);
            }

            $data = [
                'shareWith' => \implode(', ', $shares),
            ];

            $action = new self([$this->bookmark], 'update', ['data' => $data]);
            $action->executeAction();
        }

        // activity
        if (BOOKMARK_ACTIVITY_ENABLE) {
            UserActivityEventHandler::getInstance()->fireEvent('com.uz.wcf.bookmark.recentActivityEvent.share', WCF::getUser()->userID);
        }

        // reset cache
        BookmarkTopBoxCacheBuilder::getInstance()->reset();

        $successMessage = WCF::getLanguage()->get('wcf.bookmark.share.success');

        return [
            'count' => $count,
            'shareWith' => WCF::getLanguage()->get('wcf.bookmark.share.with') . ' ' . \implode(', ', $shares),
            'successMessage' => $successMessage,
        ];
    }

    /**
     * Validates parameters to view bookmarks.
     */
    public function validateViewBookmark()
    {
        $this->bookmarkShare = new BookmarkShare($this->parameters['shareID']);
        if (!$this->bookmarkShare->shareID) {
            throw new IllegalLinkException();
        }
        $this->bookmark = new Bookmark($this->bookmarkShare->bookmarkID);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        if ((WCF::getUser()->userID !== $this->bookmarkShare->receiverID)) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Opens a bookmark and sets lastVisitTime of appropriate share
     */
    public function viewBookmark()
    {
        $editor = new BookmarkShareEditor($this->bookmarkShare);
        $editor->update(['lastVisitTime' => TIME_NOW]);
        UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'unreadBookmarkCount');

        $link = '<a href="' . $this->bookmark->getUrl() . '" target="_blank" />';

        return [
            'url' => $this->bookmark->getUrl(),
        ];
    }

    /**
     * Validates parameters to refuse a bookmark.
     */
    public function validateRefuseBookmark()
    {
        $this->bookmarkShare = new BookmarkShare($this->parameters['shareID']);
        if (!$this->bookmarkShare->shareID) {
            throw new IllegalLinkException();
        }
        $this->bookmark = new Bookmark($this->bookmarkShare->bookmarkID);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        if ((WCF::getUser()->userID !== $this->bookmarkShare->receiverID)) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Refuses a bookmark and sets lastVisitTime of respective share
     */
    public function refuseBookmark()
    {
        $editor = new BookmarkShareEditor($this->bookmarkShare);
        $editor->update([
            'lastVisitTime' => TIME_NOW,
            'accepted' => 0,
            'refused' => 1,
        ]);

        $user = WCF::getUser();
        UserStorageHandler::getInstance()->reset([$user->userID], 'unreadBookmarkCount');

        UserNotificationHandler::getInstance()->fireEvent(
            'refuse',
            'com.uz.wcf.bookmark.notification.refuse',
            new BookmarkRefuseUserNotificationObject($this->bookmarkShare),
            [$this->bookmarkShare->userID],
            ['shareID' => $this->bookmarkShare->shareID]
        );

        // update shared with
        $editor = new BookmarkEditor($this->bookmark);
        $editor->update([
            'shareWith' => \str_replace($user->username, '<del>' . $user->username . '</del>', $this->bookmark->shareWith),
        ]);
    }

    /**
     * Validates parameters to accept a bookmark.
     */
    public function validateAcceptBookmark()
    {
        $this->bookmarkShare = new BookmarkShare($this->parameters['shareID']);
        if (!$this->bookmarkShare->shareID) {
            throw new IllegalLinkException();
        }
        $this->bookmark = new Bookmark($this->bookmarkShare->bookmarkID);
        if (!$this->bookmark->bookmarkID) {
            throw new IllegalLinkException();
        }

        if ((WCF::getUser()->userID !== $this->bookmarkShare->receiverID)) {
            throw new PermissionDeniedException();
        }
    }

    /**
     * Accepts a bookmark and sets lastVisitTime of respective share
     */
    public function acceptBookmark()
    {
        $editor = new BookmarkShareEditor($this->bookmarkShare);
        $editor->update([
            'lastVisitTime' => TIME_NOW,
            'accepted' => 1,
            'refused' => 0,
        ]);
        UserStorageHandler::getInstance()->reset([WCF::getUser()->userID], 'unreadBookmarkCount');

        // store bookmark
        $user = WCF::getUser();
        $data = [
            'isExternal' => (ApplicationHandler::getInstance()->isInternalURL($this->bookmark->getUrl()) ? 0 : 1),
            'isPrivate' => $this->bookmark->isPrivate,
            'remark' => $this->bookmark->remark,
            'shareWith' => '',
            'time' => TIME_NOW,
            'title' => $this->bookmark->getTitle(),
            'type' => $this->bookmark->type,
            'url' => $this->bookmark->getUrl(),
            'userID' => $user->userID,
            'username' => $user->username,
        ];

        $action = new self([], 'create', ['data' => $data]);
        $returnValues = $action->executeAction();

        // counts
        $userEditor = new UserEditor($user);
        $userEditor->updateCounters(['bookmarks' => 1]);

        $user = new User($this->bookmarkShare->userID);
        if ($user->userID) {
            $userEditor = new UserEditor($user);
            $userEditor->updateCounters(['bookmarkShares' => 1]);
        }

        // reset cache
        BookmarkTopBoxCacheBuilder::getInstance()->reset();

        // notification
        UserNotificationHandler::getInstance()->fireEvent(
            'accept',
            'com.uz.wcf.bookmark.notification.accept',
            new BookmarkAcceptUserNotificationObject($this->bookmarkShare),
            [$this->bookmarkShare->userID],
            ['shareID' => $this->bookmarkShare->shareID]
        );

        return [
            'bookmarkID' => $returnValues['returnValues']->bookmarkID,
        ];
    }
}
