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

use wcf\data\bookmark\share\ExtendedBookmarkShareList;
use wcf\system\menu\user\UserMenu;
use wcf\system\WCF;

/**
 * Shows the bookmarks shared with this user.
 */
class BookmarkSharedListPage extends MultipleLinkPage
{
    /**
     * @inheritDoc
     */
    public $loginRequired = true;

    /**
     * @inheritDoc
     */
    public $neededPermissions = ['user.bookmark.canUseBookmark'];

    /**
     * @inheritDoc
     */
    public $sqlOrderBy = 'time DESC';

    /**
     * @inheritDoc
     */
    protected function initObjectList()
    {
        $this->objectList = new ExtendedBookmarkShareList();
        $this->objectList->getConditionBuilder()->add("bookmark_share.receiverID = ?", [WCF::getUser()->userID]);
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        // set active tab
        UserMenu::getInstance()->setActiveMenuItem('wcf.user.menu.community.bookmarkShared');

        parent::show();
    }
}
