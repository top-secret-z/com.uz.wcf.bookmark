<?php
namespace wcf\system\menu\user;
use wcf\data\bookmark\share\BookmarkShare;

/**
 * UserMenuItemProvider for bookmark shares.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkMenuItemProvider extends DefaultUserMenuItemProvider {
	/**
	 * @inheritDoc
	 */
	public function isVisible() {
		// only if there are shares
		return BookmarkShare::hasShares();
	}
}
