/**
 * Opens preview of bookmark.
 * 
 * @author		2016-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.wcf.bookmark
 */
define(['Ajax'], function(Ajax) {
	"use strict";
	
	function UZBookmarkView() { this.init(); }
	UZBookmarkView.prototype = {
		init: function() {
			var buttons = elBySelAll('.jsBookmarkViewButton');
			for (var i = 0, length = buttons.length; i < length; i++) {
				buttons[i].addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
			}
		},
		
		_click: function(event) {
			event.preventDefault();
			
			Ajax.api(this, {
				actionName:	'viewBookmark',
				parameters:	{
					shareID:	~~elData(event.currentTarget, 'object-id')
				}
			});
		},
		
		_ajaxSuccess: function(data) {
			var link = elCreate('a');
			link.href = data.returnValues.url;
			window.location = link;
		},
		
		_ajaxSetup: function() {
			return {
				data: {
					className: 'wcf\\data\\bookmark\\BookmarkAction'
				}
			};
		}
	};
	
	return UZBookmarkView;
});
