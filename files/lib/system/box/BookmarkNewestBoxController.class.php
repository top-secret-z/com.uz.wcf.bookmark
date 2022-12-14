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
namespace wcf\system\box;

use wcf\system\cache\builder\BookmarkNewestBoxCacheBuilder;
use wcf\system\event\EventHandler;
use wcf\system\request\LinkHandler;
use wcf\system\WCF;

/**
 * Shows newest bookmarks.
 */
class BookmarkNewestBoxController extends AbstractDatabaseObjectListBoxController
{
    /**
     * @inheritDoc
     */
    protected static $supportedPositions = ['sidebarLeft', 'sidebarRight'];

    /**
     * @inheritDoc
     */
    public $defaultLimit = 5;

    public $maximumLimit = 25;

    public $minimumLimit = 1;

    /**
     * @inheritDoc
     */
    public $validSortFields = [];

    /**
     * @inheritDoc
     */
    protected $sortFieldLanguageItemPrefix = '';

    /**
     * cached bookmarks
     */
    protected $bookmarks = [];

    /**
     * @inheritDoc
     */
    public function getLink()
    {
        if (WCF::getUser()->userID) {
            return LinkHandler::getInstance()->getLink('User', ['object' => WCF::getUser()], '#bookmark');
        }

        return '';
    }

    /**
     * @inheritDoc
     */
    public function hasLink()
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function getObjectList()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function hasContent()
    {
        if (!MODULE_BOOKMARK || !WCF::getSession()->getPermission('user.bookmark.canViewBookmark')) {
            return false;
        }

        parent::hasContent();

        return \count($this->bookmarks) > 0;
    }

    /**
     * @inheritDoc
     */
    protected function loadContent()
    {
        $this->readObjects();

        $this->content = $this->getTemplate();
    }

    /**
     * @inheritDoc
     */
    protected function readObjects()
    {
        EventHandler::getInstance()->fireAction($this, 'readObjects');

        $this->bookmarks = BookmarkNewestBoxCacheBuilder::getInstance()->getData(['limit' => $this->limit]);
    }

    /**
     * @inheritDoc
     */
    protected function getTemplate()
    {
        return WCF::getTPL()->fetch('boxBookmarkNewest', 'wcf', [
            'bookmarks' => $this->bookmarks,
        ]);
    }
}
