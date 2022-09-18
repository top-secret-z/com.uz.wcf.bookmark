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
 * Represents a list of bookmark shares extended with bookmark infos.
 */
class ExtendedBookmarkShareList extends BookmarkShareList
{
    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'bookmark_share.time DESC';

    /**
     * @inheritDoc
     */
    public $decoratorClassName = ExtendedBookmarkShare::class;

    /**
     * @inheritDoc
     */
    public function __construct()
    {
        parent::__construct();

        if (!empty($this->sqlSelects)) {
            $this->sqlSelects .= ',';
        }
        $this->sqlSelects = "bookmark.userID AS sharerID, bookmark.username AS sharerName, bookmark.remark, bookmark.title, bookmark.url";
        $this->sqlJoins = " LEFT JOIN wcf" . WCF_N . "_bookmark bookmark ON (bookmark.bookmarkID = bookmark_share.bookmarkID)";
    }
}
