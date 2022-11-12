/**
 * Namespace for bookmarks.
 * 
 * @author        2016-2022 Zaydowicz
 * @license        GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package        com.uz.wcf.bookmark
 */
WCF.Bookmark = { };

/**
 * User Panel implementation for bookmarks.
 * 
 * @see    WCF.User.Panel.Abstract
 */
WCF.Bookmark.UserPanel = WCF.User.Panel.Abstract.extend({
    /**
     * @see    WCF.User.Panel.Abstract.init()
     */
    init: function(options) {
        options.enableMarkAsRead = true;

        this._super($('#unreadBookmarks'), 'unreadBookmarks', options);

        WCF.System.Event.addListener('com.uz.wsc.bookmark.userPanel', 'reset', (function() {
            this.resetItems();
            this.updateBadge(0);
            this._loadData = true;
        }).bind(this));

        require(['EventHandler'], (function(EventHandler) {
            EventHandler.add('com.woltlab.wcf.UserMenuMobile', 'more', (function(data) {
                if (data.identifier === 'com.uz.wsc.bookmark') {
                    this.toggle();
                }
            }).bind(this));
        }).bind(this));
    },

    /**
     * @see    WCF.User.Panel.Abstract._initDropdown()
     */
    _initDropdown: function() {
        var $dropdown = this._super();
        $('<li class="jsOnly"><a href="#" data-object-id="0" class="jsAddBookmarkMenu jsTooltip" title="' + this._options.newBookmark + '"><span class="icon icon16 fa-plus"></span> <span class="invisible">' + this._options.newBookmark + '</span></a></li>').appendTo($dropdown.getLinkList());

        require(['UZ/Bookmark/Add'], function (UZBookmarkAdd) {
            new UZBookmarkAdd('', '', '', '.jsAddBookmarkMenu');
        });

        return $dropdown;
    },

    /**
     * @see    WCF.User.Panel.Abstract._load()
     */
    _load: function() {
        this._proxy.setOption('data', {
            actionName: 'getMixedShareList',
            className: 'wcf\\data\\bookmark\\share\\BookmarkShareAction',
        });
        this._proxy.sendRequest();
    },

    /**
     * @see    WCF.User.Panel.Abstract._markAsRead()
     */
    _markAsRead: function(event, objectID) {
        this._proxy.setOption('data', {
            actionName: 'markAsRead',
            className: 'wcf\\data\\bookmark\\share\\BookmarkShareAction',
            objectIDs: [ objectID ]
        });
        this._proxy.sendRequest();
    },

    /**
     * @see    WCF.User.Panel.Abstract._markAllAsRead()
     */
    _markAllAsRead: function(event) {
        this._proxy.setOption('data', {
            actionName: 'markAllAsRead',
            className: 'wcf\\data\\bookmark\\share\\BookmarkShareAction',
        });
        this._proxy.sendRequest();
    }
});

/**
 * Loads further bookmarks for a specific user
 * 
 * @param    integer        userID
 */
WCF.Bookmark.Loader = Class.extend({
    /**
     * container object
     * @var    jQuery
     */
    _container: null,

    /**
     * access type
     * @var    string
     */
    _bookmarkAccess: 'all',

    /**
     * type
     * @var    integer
     */
    _bookmarkType: 'all',

    /**
     * button to load next events
     * @var    jQuery
     */
    _loadButton: null,

    /**
     * 'no more entries' element
     * @var    jQuery
     */
    _noMoreEntries: null,

    /**
     * action proxy
     * @var    WCF.Action.Proxy
     */
    _proxy: null,

    /**
     * user id
     * @var    integer
     */
    _userID: 0,

    _inlineEditor: null,

    /**
     * Initializes a new Bookmark Loader object.
     */
    init: function(userID) {
        this._container = $('#bookmarkList');
        this._userID = userID;

        if (!this._userID) {
            console.debug("[WCF.Bookmark.Loader] Invalid parameter 'userID' given.");
            return;
        }

        this._proxy = new WCF.Action.Proxy({
            success: $.proxy(this._success, this)
        });

        var $container = $('<li class="bookmarkListMore showMore"><button class="small">' + WCF.Language.get('wcf.bookmark.profile.moreItems') + '</button><small>' + WCF.Language.get('wcf.bookmark.profile.noMoreItems') + '</small></li>').appendTo(this._container);
        this._loadButton = $container.children('button').click($.proxy(this._click, this));
        this._noMoreEntries = $container.children('small').hide();

        if (this._container.find('> li').length == 2) {
            this._loadButton.hide();
            this._noMoreEntries.show();
        }

        $('#bookmarkAccess .button').click($.proxy(this._clickBookmarkAccess, this));
        $('#bookmarkType').on('click', 'li', $.proxy(this._clickBookmarkType, this));

        this._inlineEditor = new WCF.Bookmark.InlineEditor('.bookmark');
    },

    /**
     * Handles bookmark access change.
     */
    _clickBookmarkAccess: function(event) {
        var $button = $(event.currentTarget);
        if (this._bookmarkAccess != $button.data('bookmarkAccess')) {
            this._bookmarkAccess = $button.data('bookmarkAccess');
            $('#bookmarkAccess .button').removeClass('active');
            $button.addClass('active');
            this._reload();
        }
    },

    /**
     * Handles bookmark type change.
     */
    _clickBookmarkType: function(event) {
        $('#bookmarkTypeSelector').text($(event.currentTarget).text());
        this._bookmarkType = $(event.currentTarget).attr('id');

        this._reload();
    },

    /**
     * Handles reload.
     */
    _reload: function() {
        this._container.find('> li:not(:first-child):not(:last-child)').remove();
        this._container.data('lastBookmarkTime', 0);
        this._click();
    },

    /**
     * Loads next bookmarks.
     */
    _click: function() {
        this._loadButton.enable();

        var $parameters = {
                lastBookmarkTime: this._container.data('lastBookmarkTime'),
                userID: this._userID,
                bookmarkAccess: this._bookmarkAccess,
                bookmarkType: this._bookmarkType
        };

        this._proxy.setOption('data', {
            actionName: 'load',
            className: 'wcf\\data\\bookmark\\BookmarkAction',
            parameters: $parameters
        });
        this._proxy.sendRequest();
    },

    /**
     * Handles successful AJAX requests.
     * 
     * @param    object        data
     * @param    string        textStatus
     * @param    jQuery        jqXHR
     */
    _success: function(data, textStatus, jqXHR) {
        if (data.returnValues.template) {
            $(data.returnValues.template).insertBefore(this._loadButton.parent());

            this._container.data('lastBookmarkTime', data.returnValues.lastBookmarkTime);
            this._noMoreEntries.hide();
            this._loadButton.show().enable();
        }
        else {
            this._noMoreEntries.show();
            this._loadButton.hide();
        }

        this._inlineEditor = new WCF.Bookmark.InlineEditor('.bookmark');
    }
});

/**
 * Inline editor implementation for bookmarks.
 * 
 * @see    WCF.Inline.Editor
 */
WCF.Bookmark.InlineEditor = WCF.InlineEditor.extend({
    _setOptions: function() {
        // clear array to clean it from dropdown remains
        this._dropdowns = [];

        this._options = [
            { label: WCF.Language.get('wcf.bookmark.edit.edit'), optionName: 'edit' },
            { label: WCF.Language.get('wcf.bookmark.edit.delete'), optionName: 'delete' }
        ];

        var share = elById('bookmarkList');
        var shareEnable = elData(share, 'share-enable');

        if (shareEnable == '1') {
            this._options.push({ optionName: 'divider' });
            this._options.push({ label: WCF.Language.get('wcf.bookmark.edit.share'), optionName: 'share' });
        }
    },

    /**
     * Identify new elements and adds the event listeners to them.
     */
    rebuild: function() {
        var $elements = $(this._elementSelector);
        var self = this;
        $elements.each(function (index, element) {
            var $element = $(element);
            var $elementID = $element.wcfIdentify();

            // find trigger element
            var $trigger = self._getTriggerElement($element);
            if ($trigger === null || $trigger.length !== 1) {
                return;
            }

            $trigger.on(WCF_CLICK_EVENT, $.proxy(self._show, self)).data('elementID', $elementID);
            if (self._quickOption) {
                // simulate click on target action
                $trigger.disableSelection().data('optionName', self._quickOption).dblclick($.proxy(self._click, self));
            }

            // store reference
            self._elements[$elementID] = $element;
        });
    },

    _getTriggerElement: function(element) {
        return element.find('.jsBookmarkEditor');
    },

    _validate: function(elementID, optionName) {
        return true;
    },

    _execute: function(elementID, optionName) {
        // abort if option is invalid or not accessible
        if (!this._validate(elementID, optionName)) {
            return false;
        }

        switch (optionName) {
            case 'edit':
                require(['UZ/Bookmark/Edit'], function(UZBookmarkEdit) {
                    new UZBookmarkEdit(document.getElementById(elementID).getAttribute('data-bookmark-id'), 'edit');
                });
            break;

            case 'delete':
                require(['UZ/Bookmark/Delete'], function(UZBookmarkDelete) {
                    new UZBookmarkDelete(document.getElementById(elementID).getAttribute('data-bookmark-id'));
                });
            break;

            case 'share':
                require(['UZ/Bookmark/Share'], function(UZBookmarkShare) {
                    new UZBookmarkShare(document.getElementById(elementID).getAttribute('data-bookmark-id'));
                });
            break;
        }
    }
});
