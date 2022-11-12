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
namespace wcf\system\bulk\processing\user;

use wcf\data\DatabaseObjectList;
use wcf\data\user\UserList;
use wcf\system\cache\builder\BookmarkNewestBoxCacheBuilder;
use wcf\system\cache\builder\BookmarkTopBoxCacheBuilder;
use wcf\system\database\util\PreparedStatementConditionBuilder;
use wcf\system\user\storage\UserStorageHandler;
use wcf\system\WCF;

/**
 * Bulk processing action implementation for deleting bookmarks.
 */
class BookmarkDeleteBulkProcessingAction extends AbstractUserBulkProcessingAction
{
    /**
     * @inheritDoc
     */
    public function executeAction(DatabaseObjectList $objectList)
    {
        if (!($objectList instanceof UserList)) {
            return;
        }

        $userIDs = $objectList->getObjectIDs();

        if (!empty($userIDs)) {
            // delete bookmarks; will delete shares automatically
            $conditions = new PreparedStatementConditionBuilder();
            $conditions->add("userID IN (?)", [$userIDs]);

            // update users
            $sql = "UPDATE    wcf" . WCF_N . "_user
                    SET bookmarks = 0, bookmarkShares = 0
                " . $conditions;
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());

            // delete bookmarks
            $conditions = new PreparedStatementConditionBuilder();
            $conditions->add("userID IN (?)", [$userIDs]);
            $sql = "DELETE FROM    wcf" . WCF_N . "_bookmark
                " . $conditions;
            $statement = WCF::getDB()->prepareStatement($sql);
            $statement->execute($conditions->getParameters());

            // reset caches
            BookmarkNewestBoxCacheBuilder::getInstance()->reset();
            BookmarkTopBoxCacheBuilder::getInstance()->reset();

            // reset user storage
            UserStorageHandler::getInstance()->reset($userIDs, 'unreadBookmarkCount');
        }
    }

    /**
     * @inheritDoc
     */
    public function getObjectList()
    {
        $userList = parent::getObjectList();

        // only users with bookmarks
        $userList->getConditionBuilder()->add("(user_table.bookmarks > 0 OR user_table.bookmarkShares > 0)");

        return $userList;
    }
}
