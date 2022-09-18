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
namespace wcf\acp\page;

use wcf\data\bookmark\share\AdministrativeBookmarkShareList;
use wcf\data\user\User;
use wcf\page\SortablePage;
use wcf\system\WCF;
use wcf\util\StringUtil;

/**
 * Shows all bookmark shares.
 */
class BookmarkShareListPage extends SortablePage
{
    /**
     * @inheritDoc
     */
    public $activeMenuItem = 'wcf.acp.menu.link.bookmarkShare.list';

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['admin.user.canEditUser'];

    /**
     * @inheritDoc
     */
    public $defaultSortField = 'time';

    /**
     * @inheritDoc
     */
    public $defaultSortOrder = 'DESC';

    /**
     * @inheritDoc
     */
    public $validSortFields = ['shareID', 'username', 'receiverName', 'shareWith', 'url', 'title', 'remark'];

    /**
     * filter data
     */
    public $username = '';

    /**
     * @inheritDoc
     */
    public $objectListClassName = AdministrativeBookmarkShareList::class;

    /**
     * @inheritDoc
     */
    public function readParameters()
    {
        parent::readParameters();

        if (!empty($_REQUEST['username'])) {
            $this->username = StringUtil::trim($_REQUEST['username']);
        }
    }

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        parent::initObjectList();

        // apply filter
        if (!empty($this->username)) {
            $user = User::getUserByUsername($this->username);
            if ($user->userID) {
                $this->objectList->getConditionBuilder()->add('bookmark_share.userID = ?', [$user->userID]);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function assignVariables()
    {
        parent::assignVariables();

        WCF::getTPL()->assign([
            'username' => $this->username,
        ]);
    }
}
