/**
 * Refuses a bookmark.
 * 
 * @author        2016-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.wcf.bookmark
 */
define(['Ajax', 'Ui/Notification', 'Language', 'Dom/Traverse', 'Ui/Confirmation'], function(Ajax, UiNotification, Language, DomTraverse, UiConfirmation) {
    "use strict";

    function UZBookmarkRefuse() { this.init(); }
    UZBookmarkRefuse.prototype = {
        init: function() {
            var buttons = elBySelAll('.jsBookmarkRefuseButton');
            for (var i = 0, length = buttons.length; i < length; i++) {
                buttons[i].addEventListener(WCF_CLICK_EVENT, this._click.bind(this));
            }
        },

        _click: function(event) {
            event.preventDefault();

            var objectID = ~~elData(event.currentTarget, 'object-id');

            UiConfirmation.show({
                confirm: function() {
                    Ajax.apiOnce({
                        data: {
                            actionName: 'refuseBookmark',
                            className: 'wcf\\data\\bookmark\\BookmarkAction',
                            parameters:    {
                                shareID: objectID
                            }
                        },
                        success: function() {
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
                            newSpan.classList.add('red');
                            newSpan.innerHTML = Language.get('wcf.bookmark.share.refused');
                            target.insertBefore(newSpan, target.firstChild);

                            UiNotification.show();
                        }
                    });
                },
                message: Language.get('wcf.bookmark.share.refuse.confirm')
            });
        }
    };

    return UZBookmarkRefuse;
});
