/**
 * Provides the dialog to add a new bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
define(['Ajax', 'Dom/Util', 'Dom/Traverse', 'Language', 'Ui/Dialog', 'Ui/Notification'], function(Ajax, DomUtil, DomTraverse, Language, UiDialog, UiNotification) {
	"use strict";
	
	function UZBookmarkAdd(type, title, url, jsButton) { this.init(type, title, url, jsButton); }
	UZBookmarkAdd.prototype = {
		init: function(type, title, url, jsButton) {
			this._type = type;
			this._title = title;
			this._url = url;
			
			this._forceRemark = 1;
			if (typeof BOOKMARK_FORCE_REMARK !== 'undefined') {
				this._forceRemark = BOOKMARK_FORCE_REMARK;
			}
			
			var button = elBySel(jsButton);
			button.addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
			this._objectID = ~~elAttr(button, 'data-object-id');
		},
		
		_click: function(event) {
			event.preventDefault();
			
			Ajax.api(this, {
				actionName: 'getAddBookmarkDialog',
				parameters: {
					title: 	this._title,
					type: 	this._type,
					objectID: 	this._objectID,
					url: 	this._url
				}
			});
		},
		
		_ajaxSuccess: function(data) {
			switch (data.actionName) {
				case 'addBookmark':
					UiNotification.show();
					UiDialog.close(this);
					break;
				
				case 'getAddBookmarkDialog':
					this._render(data);
					break;
			}
		},
		
		_render: function(data) {
			UiDialog.open(this, data.returnValues.template);
			
			var submitButton = elBySel('.jsSubmitBookmark');
			submitButton.addEventListener('click', this._submit.bind(this));
		},
		
		_submit: function() {
			// check title
			var titleInput = elBySel('.jsBookmarkTitle');
			var title = titleInput.value;
			var titleError = DomTraverse.nextByClass(titleInput, 'innerError');
			
			title = title.trim();
			if (title == '') {
				if (!titleError) {
					titleError = elCreate('small');
					titleError.className = 'innerError';
					titleError.innerText = Language.get('wcf.global.form.error.empty');
					DomUtil.insertAfter(titleError, titleInput);
					titleInput.closest('dl').classList.add('formError');
				}
				else {
					titleError.innerText = Language.get('wcf.global.form.error.empty');
				}
				return;
			}
			else if (title.length > 255) {
				if (!titleError) {
					titleError = elCreate('small');
					titleError.className = 'innerError';
					titleError.innerText = Language.get('wcf.bookmark.title.error.tooLong');
					DomUtil.insertAfter(titleError, titleInput);
					titleInput.closest('dl').classList.add('formError');
				}
				else {
					titleError.innerText = Language.get('wcf.bookmark.title.error.tooLong');
				}
				return;
			}
			else {
				if (titleError) {
					elRemove(titleError);
					titleInput.closest('dl').classList.remove('formError');
				}
			}
			
			// check url with very basic format check
			var urlInput = elBySel('.jsBookmarkUrl');
			var url = urlInput.value;
			var urlError = DomTraverse.nextByClass(urlInput, 'innerError');
			var regex = new RegExp("(https?|ftp):\/\/.*\..");
			
			url = url.trim();
			if (url == '') {
				if (!urlError) {
					urlError = elCreate('small');
					urlError.className = 'innerError';
					urlError.innerText = Language.get('wcf.global.form.error.empty');
					DomUtil.insertAfter(urlError, urlInput);
					urlInput.closest('dl').classList.add('formError');
				}
				else {
					urlError.innerText = Language.get('wcf.global.form.error.empty');
				}
				return;
			}
			else if (!regex.test(url)) {
				if (!urlError) {
					urlError = elCreate('small');
					urlError.className = 'innerError';
					urlError.innerText = Language.get('wcf.bookmark.url.error.invalid');
					DomUtil.insertAfter(urlError, urlInput);
					urlInput.closest('dl').classList.add('formError');
				}
				else {
					urlError.innerText = Language.get('wcf.bookmark.url.error.invalid');
				}
				return;
			}
			else {
				if (urlError) {
					elRemove(urlError);
					urlInput.closest('dl').classList.remove('formError');
				}
			}
			
			// check remark
			var remarkInput = elBySel('.jsBookmarkRemark');
			var remark = remarkInput.value;
			var remarkError = DomTraverse.nextByClass(remarkInput, 'innerError');
			
			remark = remark.trim();
			
			if (this._forceRemark && remark == '') {
				if (!remarkError) {
					remarkError = elCreate('small');
					remarkError.className = 'innerError';
					remarkError.innerText = Language.get('wcf.global.form.error.empty');
					DomUtil.insertAfter(remarkError, remarkInput);
					remarkInput.closest('dl').classList.add('formError');
				}
				return;
			}
			else {
				if (remarkError) {
					elRemove(remarkError);
					remarkInput.closest('dl').classList.remove('formError');
				}
			}
			
			// access
			var access = 0;
			if (elById('access1').checked) { access = 1; }
			if (elById('access2').checked) { access = 2; }
			
			// everything is fine, send
			Ajax.api(this, {
				actionName: 'addBookmark',
				parameters: {
					title:	title,
					url:	url,
					remark:	remark,
					access:	access,
					type:	this._type,
					objectID: 	this._objectID,
					action:	'add'
				}
			});
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					className: 'wcf\\data\\bookmark\\BookmarkAction'
				}
			};
		},
		
		_dialogSetup: function() {
			return {
				id: 'addBookmark',
				options: {
					title: Language.get('wcf.bookmark.add')
				},
				source: null
			};
		}
	};
	
	return UZBookmarkAdd;
});
