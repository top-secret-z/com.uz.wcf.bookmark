/**
 * Provides the dialog to edit a bookmark.
 * 
 * @author        2016-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.wcf.bookmark
 */
define(['Ajax', 'Dom/Util', 'Dom/Traverse', 'Language', 'Ui/Dialog', 'Ui/Notification'], function(Ajax, DomUtil, DomTraverse, Language, UiDialog, UiNotification) {
    "use strict";

    function UZBookmarkEdit(bookmarkID, modus) { this.init(bookmarkID, modus); }

    UZBookmarkEdit.prototype = {
        init: function(bookmarkID, modus) {
            this._bookmarkID = bookmarkID;
            this._modus = modus;

            this._forceRemark = 1;
            if (typeof BOOKMARK_FORCE_REMARK !== 'undefined') {
                this._forceRemark = BOOKMARK_FORCE_REMARK;
            }

            Ajax.api(this, {
                actionName: 'getEditBookmarkDialog',
                parameters: {
                    bookmarkID: this._bookmarkID
                }
            });
        },

        _ajaxSuccess: function(data) {
            switch (data.actionName) {
                case 'editBookmark':
                    UiNotification.show();
                    UiDialog.close(this);

                    break;

                case 'getEditBookmarkDialog':
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
            var titleError = DomTraverse.nextByClass(titleInput, 'innerError');
            var title = titleInput.value;

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

            // check remark
            var remarkInput = elBySel('.jsBookmarkRemark');
            var remarkError = DomTraverse.nextByClass(remarkInput, 'innerError');
            var remark = remarkInput.value;

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

            // everything is fine, update and send
            if (this._modus == 'edit') {
                var titleField = elById('title' + this._bookmarkID);
                var remarkField = elById('remark' + this._bookmarkID);
                titleField.innerHTML = title;
                remarkField.innerHTML = remark;
            }

            Ajax.api(this, {
                actionName: 'editBookmark',
                parameters: {
                    bookmarkID:    this._bookmarkID,
                    access:    access,
                    remark:    remark,
                    title:    title
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
                    title: Language.get('wcf.bookmark.edit')
                },
                source: null
            };
        }
    };

    return UZBookmarkEdit;
});
