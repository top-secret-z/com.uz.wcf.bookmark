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
namespace wcf\system\cache\builder;

use wcf\data\bookmark\BookmarkList;

/**
 * Caches the newest bookmarks.
 */
class BookmarkNewestBoxCacheBuilder extends AbstractCacheBuilder
{
    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    /**
     * @inheritDoc
     */
    protected $maxLifetime = 300;

    /**
     * @inheritDoc
     */
    protected function rebuild(array $parameters)
    {
        if (!MODULE_BOOKMARK) {
            return [];
        }

        $bookmarkList = new BookmarkList();
        $bookmarkList->getConditionBuilder()->add("isPrivate = 0");
        $bookmarkList->sqlOrderBy = 'time DESC';
        $bookmarkList->sqlLimit = !empty($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
        $bookmarkList->readObjects();

        return $bookmarkList->getObjects();
    }
}
