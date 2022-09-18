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
namespace wcf\data\bookmark\share;

use wcf\data\bookmark\Bookmark;
use wcf\data\DatabaseObject;
use wcf\data\user\User;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;
use wcf\util\DateUtil;

/**
 * Represents a bookmark.
 */
class BookmarkShare extends DatabaseObject
{
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
    public function getTitle()
    {
        return $this->notice;
    }

    /**
     * Returns bookmark share url.
     */
    public function getUrl()
    {
        $bookmark = new Bookmark($this->bookmarkID);

        return $bookmark->getUrl();
    }

    /**
     * Returns the bookmark.
     */
    public function getBookmark()
    {
        $bookmark = new Bookmark($this->bookmarkID);
        if (!$bookmark->bookmarkID) {
            return null;
        }

        return $bookmark;
    }

    /**
     * Get user's shares for a specific bookmark
     */
    public static function getShares($bookmarkID)
    {
        $shareList = new BookmarkShareList();
        $shareList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);
        $shareList->getConditionBuilder()->add('refused = ?', [0]);
        $shareList->getConditionBuilder()->add('accepted = ?', [1]);
        $shareList->getConditionBuilder()->add('bookmarkID = ?', [$bookmarkID]);
        $shareList->readObjects();
        $shares = $shareList->getObjects();

        if (!\count($shares)) {
            return '';
        }

        $temp = [];
        foreach ($shares as $share) {
            $user = UserProfileRuntimeCache::getInstance()->getObject($share->receiverID);
            if ($user->userID) {
                $temp[] = "<a href=" . $user->getLink() . ' class="userLink" data-user-id="' . $user->userID . '">' . $user->username . '</a>';
            }
        }

        if (\count($temp)) {
            return \implode(', ', $temp);
        }

        return '';
    }

    /**
     * Returns the readable period matching this bookmark share
     * copied from
     * Provides a default implementation for user notification events.
     *
     * @author    Joshua Ruesweg, Marcel Werk, Oliver Kliebisch
     * @copyright    2001-2016 WoltLab GmbH, Oliver Kliebisch.
     *
     */
    public function getPeriod()
    {
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
    public function getUserProfile()
    {
        if ($this->userProfile === null) {
            $this->userProfile = UserProfileRuntimeCache::getInstance()->getObject($this->userID);
        }

        return $this->userProfile;
    }

    /**
     * Check whether user has shares
     */
    public static function hasShares()
    {
        if (self::$hasShares === null) {
            $sql = "SELECT    COUNT(*) as count
                    FROM    wcf" . WCF_N . "_bookmark_share
                    WHERE    receiverID = ?";
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute([WCF::getUser()->userID]);
            $row = $statement->fetchArray();
            self::$hasShares = ($row['count'] ? true : false);
        }

        return self::$hasShares;
    }
}
