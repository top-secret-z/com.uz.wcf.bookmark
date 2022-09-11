<?php
namespace wcf\system\cache\builder;
use wcf\data\user\UserList;
use wcf\system\cache\builder\AbstractCacheBuilder;

/**
 * Caches the top bookmark share users.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkTopBoxCacheBuilder extends AbstractCacheBuilder {
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
		if (!MODULE_BOOKMARK) return array();
		
		$userList = new UserList();
		$userList->getConditionBuilder()->add('user_table.bookmarkShares > 0');
		$userList->sqlOrderBy = 'user_table.bookmarkShares DESC';
		$userList->sqlLimit = !empty($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
		$userList->readObjectIDs();
		
		return $userList->getObjectIDs();
	}
}
