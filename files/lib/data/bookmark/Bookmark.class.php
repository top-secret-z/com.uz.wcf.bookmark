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

use wcf\data\DatabaseObject;
use wcf\data\user\User;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Represents a bookmark.
 */
class Bookmark extends DatabaseObject
{
    /**
     * @inheritDoc
     */
    protected static $databaseTableName = 'bookmark';

    /**
     * @inheritDoc
     */
    protected static $databaseTableIndexName = 'bookmarkID';

    /**
     * user profile object
     */
    protected $userProfile;

    /**
     * profile of user who shared
     */
    protected $shareProfile;

    /**
     * Returns user profile matching this bookmark.
     */
    public function getUserProfile()
    {
        if ($this->userProfile === null) {
            $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
        }

        return $this->userProfile;
    }

    /**
     * Returns share user profile from userID.
     */
    public function getShareProfile($userID)
    {
        if ($this->shareProfile === null) {
            $this->shareProfile = UserProfileRuntimeCache::getInstance()->getObject($userID);
        }

        return $this->shareProfile;
    }

    /**
     * Returns bookmark title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns bookmark remark.
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Returns bookmark url.
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Returns the object type name.
     */
    public function getObjectTypeName()
    {
        return $this->type;
    }

    /**
     * Check with a bookmark with this type and objectID already exist.
     */
    public static function checkExist($type, $objectID)
    {
        $sql = "SELECT    COUNT(*) as count
                FROM    wcf" . WCF_N . "_bookmark
                WHERE    type = ? AND objectID = ? AND userID = ?";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute([$type, $objectID, WCF::getUser()->userID]);

        return $statement->fetchColumn();
    }

    /**
     * Returns true if user has permission to see the bookmark.
     */
    public function canSee()
    {
        if (WCF::getSession()->getPermission('mod.bookmark.canModerateBookmark')) {
            return true;
        }

        if (!WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
            return false;
        }

        if ($this->userID == WCF::getUser()->userID) {
            return true;
        }

        switch ($this->isPrivate) {
            case 1:
                return false;
                break;
            case 2:
                if (!WCF::getUserProfileHandler()->isFollowing($this->userID)) {
                    return false;
                }
                break;
        }

        return true;
    }
}
