<?php
namespace wcf\system\stat;
use wcf\system\WCF;

/**
 * Stat handler implementation for bookmark stats.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
class BookmarkStatDailyHandler extends AbstractStatDailyHandler {
	/**
	 * @inheritDoc
	 */
	public function getData($date) {
		return [
				'counter' => $this->getCounter($date, 'wcf'.WCF_N.'_bookmark', 'time'),
				'total' => $this->getTotal($date, 'wcf'.WCF_N.'_bookmark', 'time')
		];
	}
}
