<?php
namespace wcf\system\cache\builder;
use wcf\data\bookmark\BookmarkList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the newest bookmarks.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkNewestBoxCacheBuilder extends AbstractCacheBuilder {
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
	protected function rebuild(array $parameters) {
		if (!MODULE_BOOKMARK) return [];
		
		$bookmarkList = new BookmarkList();
		$bookmarkList->getConditionBuilder()->add("isPrivate = 0");
		$bookmarkList->sqlOrderBy = 'time DESC';
		$bookmarkList->sqlLimit = !empty($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
		$bookmarkList->readObjects();
		
		return $bookmarkList->getObjects();
	}
}
