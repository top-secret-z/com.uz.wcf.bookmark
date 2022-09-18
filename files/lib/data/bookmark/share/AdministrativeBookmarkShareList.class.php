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

use wcf\system\WCF;

/**
 * Represents a list of administrative bookmark shares.
 */
class AdministrativeBookmarkShareList extends BookmarkShareList
{
    /**
     * @inheritDoc
     */
    public $decoratorClassName = AdministrativeBookmarkShare::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        $this->sqlSelects = 'bookmark_table.type, bookmark_table.url, bookmark_table.shareWith';
        $this->sqlJoins = "LEFT JOIN wcf" . WCF_N . "_bookmark bookmark_table ON (bookmark_table.bookmarkID = bookmark_share.bookmarkID)";
    }

    /**
     * Returns a list of available bookmark types.
     */
    public function getAvailableTypes()
    {
        $fileTypes = [];
        $sql = "SELECT    DISTINCT bookmark.type
                FROM    wcf" . WCF_N . "_bookmark bookmark
                " . $this->getConditionBuilder();
        $statement = WCF::getDB()->prepareStatement($sql);
        $statement->execute($this->getConditionBuilder()->getParameters());
        while ($row = $statement->fetchArray()) {
            $types[$row['type']] = $row['type'];
        }

        \ksort($types);

        return $types;
    }
}
