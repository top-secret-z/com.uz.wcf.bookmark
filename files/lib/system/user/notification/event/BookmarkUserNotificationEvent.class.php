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
namespace wcf\system\user\notification\event;

use wcf\data\bookmark\Bookmark;
use wcf\data\bookmark\share\BookmarkShare;

/**
 * User notification event for bookmarks.
 */
class BookmarkUserNotificationEvent extends AbstractUserNotificationEvent
{
    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->getLanguage()->get('wcf.user.notification.bookmark.title');
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        $bookmark = $this->getUserNotificationObject()->getBookmark();
        $share = new BookmarkShare($this->additionalData['shareID']);
        if (!$share->shareID) {
            $remark = '';
        }
        $remark = $share->remark;

        return $this->getLanguage()->getDynamicVariable('wcf.user.notification.bookmark.message', [
            'author' => $this->author,
            'url' => $bookmark->url,
            'title' => $bookmark->title,
            'remark' => $remark,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant')
    {
        return [
            'message-id' => 'wcf.user.notification.bookmark/' . $this->getUserNotificationObject()->shareID,
            'template' => 'email_notification_bookmark',
            'application' => 'wcf',
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        return $this->userNotificationObject->getUrl();
    }

    /**
     * @inheritDoc
     */
    public function checkAccess()
    {
        return true;
    }
}
