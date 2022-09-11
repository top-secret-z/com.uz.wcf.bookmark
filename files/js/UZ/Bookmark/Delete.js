/**
 * Handles deletion of a bookmark in user profile tab.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
define(['Ajax', 'Language', 'Ui/Confirmation', 'Ui/Notification'], function(Ajax, Language, UiConfirmation, UiNotification) {
	"use strict";
	
	function UZBookmarkDelete(bookmarkID) { this.init(bookmarkID); }
	
	UZBookmarkDelete.prototype = {
		init: function(bookmarkID) {
			var bookmarkID = bookmarkID;
			var selected = elById('bookmark' + bookmarkID);
			
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'deleteBookmark',
							className: 'wcf\\data\\bookmark\\BookmarkAction',
							parameters: {
								bookmarkID: bookmarkID
							}
						},
						success: function() {
							elRemove(selected);
							UiNotification.show();
						}
					});
				},
				message: Language.get('wcf.bookmark.edit.delete.confirm')
			});	
		}
	};
	
	return UZBookmarkDelete;
});
