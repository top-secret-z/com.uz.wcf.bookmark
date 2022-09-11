/**
 * Share a bookmark wit other users.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
define(['Ajax', 'Language', 'Ui/Dialog', 'Ui/Notification', 'WoltLabSuite/Core/Ui/ItemList/User'], function(Ajax, Language, UiDialog, UiNotification, UiItemListUser) {
	"use strict";
	
	function UZBookmarkShare(bookmarkID) { this.init(bookmarkID); }
	
	UZBookmarkShare.prototype = {
		/**
		 * Manages the form to share a bookmark with one or more users.
		 * 
		 * @param	{int}	bookmarkID
		 */
		init: function(bookmarkID) {
			this._bookmarkID = bookmarkID;
			
			Ajax.api(this, {
				actionName: 'getShareBookmarkDialog'
			});
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					className: 'wcf\\data\\bookmark\\BookmarkAction',
					objectIDs: [ this._bookmarkID ]
				}
			};
		},
		
		_ajaxSuccess: function(data) {
			switch (data.actionName) {
				case 'shareBookmark':
					this._handleResponse(data);
					break;
				
				case 'getShareBookmarkDialog':
					this._render(data);
					break;
			}
		},
		
		_handleResponse: function(data) {
			if (data.returnValues.count) {
				UiNotification.show(data.returnValues.successMessage);
			}
			
			UiDialog.close(this);
			
			// update shareWith
			var shareWith = elById('shareWith' + this._bookmarkID);
			shareWith.textContent = data.returnValues.shareWith;
		},
		
		/**
		 * Renders the dialog to share to users.
		 * 
		 * @param	{object}	data		response data
		 */
		_render: function(data) {
			UiDialog.open(this, data.returnValues.template);
			
			var buttonSubmit = document.getElementById('shareBookmark');
			buttonSubmit.disabled = true;
			
			UiItemListUser.init('bookmarkReceiverInput', {
					callbackChange: function(elementId, values) { buttonSubmit.disabled = (values.length === 0); },
					excludedSearchValues: data.returnValues.excludedSearchValues,
					maxItems: data.returnValues.maxItems
			});
			
			buttonSubmit.addEventListener('click', this._submit.bind(this));
		},
		
		_submit: function() {
			var values = UiItemListUser.getValues('bookmarkReceiverInput'), receivers = [];
			for (var i = 0, length = values.length; i < length; i++) {
				receivers.push(values[i].value);
			}
			var remark = elBySel('.jsBookmarkRemark').value;
			
			Ajax.api(this, {
				actionName: 'shareBookmark',
				parameters: {
					bookmarkID: this._bookmarkID,
					receivers: receivers,
					remark: remark.trim()
				}
			});
		},
		
		_dialogSetup: function() {
			return {
				id: 'bookmarkShareDialog',
				options: {
					title: Language.get('wcf.bookmark.share.title')
				},
				source: null
			};
		}
	};
	
	return UZBookmarkShare;
});
