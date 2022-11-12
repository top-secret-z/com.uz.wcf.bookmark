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
use wcf\data\DatabaseObjectDecorator;
use wcf\data\user\User;
use wcf\data\user\UserProfile;
use wcf\system\cache\runtime\UserProfileRuntimeCache;
use wcf\system\WCF;

/**
 * Represents a bookmark.
 */
class ExtendedBookmarkShare extends DatabaseObjectDecorator
{
    /**
     * @inheritDoc
     */
    protected static $baseClass = BookmarkShare::class;

    /**
     * user profile object
     */
    protected $sharerProfile;

    /**
     * Returns bookmark share title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns bookmark share url.
     */
    public function getUrl()
    {
        $bookmark = new Bookmark($this->bookmarkID);

        return $this->url;
    }

    /**
     * Returns the user profile object.
     */
    public function getSharerProfile()
    {
        if ($this->sharerProfile === null) {
            if ($this->sharerID) {
                $this->sharerProfile = UserProfileRuntimeCache::getInstance()->getObject($this->sharerID);
            } else {
                $this->sharerProfile = UserProfile::getGuestUserProfile($this->sharerName);
            }
        }

        return $this->sharerProfile;
    }
}
