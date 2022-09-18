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
namespace wcf\system\menu\user\profile\content;

use wcf\data\bookmark\BookmarkList;
use wcf\system\SingletonFactory;
use wcf\system\WCF;

/**
 * Handles user profile bookmark content.
 */
class BookmarkUserProfileMenuContent extends SingletonFactory implements IUserProfileMenuContent
{
    /**
     * @inheritDoc
     */
    public function getContent($userID)
    {
        $bookmarkList = new BookmarkList();

        $bookmarkList->sqlLimit = 15;

        $bookmarkList->getConditionBuilder()->add("userID = ?", [$userID]);

        if (WCF::getUser()->userID != $userID) {
            // follower?
            if (WCF::getUserProfileHandler()->isFollowing($userID)) {
                $bookmarkList->getConditionBuilder()->add("(isPrivate = ? OR isPrivate = ?)", [0, 2]);
            } else {
                $bookmarkList->getConditionBuilder()->add("isPrivate = ?", [0]);
            }
        }
        $bookmarkList->sqlOrderBy = "time DESC";
        $bookmarkList->readObjects();

        WCF::getTPL()->assign([
            'bookmarkList' => $bookmarkList,
            'userID' => $userID,
            'lastBookmarkTime' => $bookmarkList->getLastBookmarkTime(),
            'shareEnable' => BOOKMARK_SHARE_ENABLE,
        ]);

        return WCF::getTPL()->fetch('userProfileBookmark');
    }

    /**
     * @inheritDoc
     */
    public function isVisible($userID)
    {
        if (WCF::getUser()->userID == $userID) {
            return true;
        }
        if (WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
            return true;
        }

        return false;
    }
}
