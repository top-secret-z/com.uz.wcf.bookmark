<?php
namespace wcf\data\bookmark\share;
use wcf\system\WCF;

/**
 * Represents a list of administrative bookmark shares.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class AdministrativeBookmarkShareList extends BookmarkShareList {
	/**
	 * @inheritDoc
	 */
	public $decoratorClassName = AdministrativeBookmarkShare::class;
	
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		
		$this->sqlSelects = 'bookmark_table.type, bookmark_table.url, bookmark_table.shareWith';
		$this->sqlJoins = "LEFT JOIN wcf".WCF_N."_bookmark bookmark_table ON (bookmark_table.bookmarkID = bookmark_share.bookmarkID)";
	}
	
	/**
	 * Returns a list of available bookmark types.
	 */
	public function getAvailableTypes() {
		$fileTypes = [];
		$sql = "SELECT	DISTINCT bookmark.type
				FROM	wcf".WCF_N."_bookmark bookmark
				".$this->getConditionBuilder();
		$statement = WCF::getDB()->prepareStatement($sql);
		$statement->execute($this->getConditionBuilder()->getParameters());
		while ($row = $statement->fetchArray()) {
			$types[$row['type']] = $row['type'];
		}
		
		ksort($types);
		
		return $types;
	}
}
