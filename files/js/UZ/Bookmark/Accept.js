/**
 * Accept a bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
define(['Ajax', 'Ui/Notification', 'Language', 'Dom/Traverse', 'Ui/Confirmation'], function(Ajax, UiNotification, Language, DomTraverse, UiConfirmation) {
	"use strict";
	
	function UZBookmarkAccept() { this.init(); }
	UZBookmarkAccept.prototype = {
		init: function() {
			var buttons = elBySelAll('.jsBookmarkAcceptButton');
			for (var i = 0, length = buttons.length; i < length; i++) {
				buttons[i].addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
			}
		},
		
		_click: function(event) {
			event.preventDefault();
			
			var objectID = ~~elData(event.currentTarget, 'object-id');
			var id = 'divID' + objectID;
			
			UiConfirmation.show({
				confirm: function() {
					Ajax.apiOnce({
						data: {
							actionName: 'acceptBookmark',
							className: 'wcf\\data\\bookmark\\BookmarkAction',
							parameters:	{
								shareID: objectID
							}
						},
						success: function(data) {
							// set badges and buttons
							var accept = elById('accept' + objectID);
							var refuse = elById('refuse' + objectID);
							accept.parentNode.removeChild(accept);
							refuse.parentNode.removeChild(refuse);
							
							var target = elById('divID' + objectID);
							var oldSpan = target.getElementsByClassName('badge');
							
							// remove any old badges
							if (oldSpan.length) {
								target.removeChild(oldSpan[0]);
							}
							
							// set new
							var newSpan = elCreate('span');
							newSpan.classList.add('badge');
							newSpan.classList.add('label');
							newSpan.classList.add('green');
							newSpan.innerHTML = Language.get('wcf.bookmark.share.accepted');
							target.insertBefore(newSpan, target.firstChild);
							
							UiNotification.show();
							
							// open edit dialog
							require(['UZ/Bookmark/Edit'], function (UZBookmarkEdit) {
								new UZBookmarkEdit(data.returnValues.bookmarkID, 'accept');
							});
						}
					});
				},
				message: Language.get('wcf.bookmark.share.accept.confirm')
			});
		}
	};
	
	return UZBookmarkAccept;
});
