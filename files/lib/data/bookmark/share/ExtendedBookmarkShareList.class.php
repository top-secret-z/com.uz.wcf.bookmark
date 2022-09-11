<?php
namespace wcf\data\bookmark\share;
use wcf\system\WCF;

/**
 * Represents a list of bookmark shares extended with bookmark infos.
 *  
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class ExtendedBookmarkShareList extends BookmarkShareList  {
	/**
	 * @inheritDoc
	 */
	public $sqlOrderBy = 'bookmark_share.time DESC';
	
	/**
	 * @inheritDoc
	 */
	public $decoratorClassName = ExtendedBookmarkShare::class;
	
	/**
	 * @inheritDoc
	 */
	public function __construct() {
		parent::__construct();
		
		if (!empty($this->sqlSelects)) $this->sqlSelects .= ',';
		$this->sqlSelects = "bookmark.userID AS sharerID, bookmark.username AS sharerName, bookmark.remark, bookmark.title, bookmark.url";
		$this->sqlJoins = " LEFT JOIN wcf".WCF_N."_bookmark bookmark ON (bookmark.bookmarkID = bookmark_share.bookmarkID)";
	}
}	