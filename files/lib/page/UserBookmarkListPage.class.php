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
namespace wcf\page;

use wcf\data\bookmark\BookmarkList;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows the bookmarks of this user.
 */
class UserBookmarkListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'com.uz.wcf.bookmark.List';

    /**
     * @inheritDoc
     */
    public $neededModules = ['MODULE_BOOKMARK'];

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.bookmark.canUseBookmark'];

    /**
     * @inheritDoc
     */
    public $objectListClassName = BookmarkList::class;

    /**
     * @inheritDoc
     */
    public $itemsPerPage = 15;

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'time';

    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['time', 'type', 'title', 'remark'];

    /**
     * filter
     */
    public $search = '';

    public $type = '';

    public $availableTypes = [];

    public $isPrivate = '';

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['search'])) {
            $this->search = StringUtil::trim($_REQUEST['search']);
        }
        if (!empty($_REQUEST['isPrivate'])) {
            $this->isPrivate = $_REQUEST['isPrivate'];
        }
        if (!empty($_REQUEST['type'])) {
            $this->type = $_REQUEST['type'];
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        // user
        $this->objectList->getConditionBuilder()->add('userID = ?', [WCF::getUser()->userID]);

        // get data
        $this->availableTypes = $this->objectList->getAvailableTypes();

        // filter
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $this->objectList->getConditionBuilder()->add('title LIKE ? OR remark LIKE ?', [$search, $search]);
        }

        if (!empty($this->isPrivate)) {
            $this->objectList->getConditionBuilder()->add('isPrivate = ?', [$this->isPrivate]);
        }

        if (!empty($this->type)) {
            $this->objectList->getConditionBuilder()->add('type = ?', [$this->type]);
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'search' => $this->search,
            'isPrivate' => $this->isPrivate,
            'type' => $this->type,
            'availableTypes' => $this->availableTypes,
            'shareEnable' => BOOKMARK_SHARE_ENABLE,
        ]);
    }
}
