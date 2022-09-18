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

use wcf\data\DatabaseObjectList;
use wcf\system\WCF;

/**
 * Represents a list of bookmarks.
 */
class BookmarkList extends DatabaseObjectList
{
    /**
     * @inheritDoc
     */
    public $className = Bookmark::class;

    /**
     * Returns timestamp of oldest bookmark fetched.
     */
    public function getLastBookmarkTime()
    {
        $lastBookmarkTime = 0;
        foreach ($this->objects as $bookmark) {
            if (!$lastBookmarkTime) {
                $lastBookmarkTime = $bookmark->time;
            }

            $lastBookmarkTime = \min($lastBookmarkTime, $bookmark->time);
        }

        return $lastBookmarkTime;
    }

    /**
     * Returns a list of available bookmark types.
     */
    public function getAvailableTypes()
    {
        $types = [];
        $sql = "SELECT    DISTINCT bookmark.type
                FROM    wcf" . WCF_N . "_bookmark bookmark";
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute();
        while ($row = $statement->fetchArray()) {
            if ($row['type']) {
                $types[$row['type']] = WCF::getLanguage()->get('wcf.bookmark.type.' . $row['type']);
            }
        }

        \ksort($types);

        return $types;
    }
}
