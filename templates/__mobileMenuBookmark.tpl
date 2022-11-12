{if MODULE_BOOKMARK && BOOKMARK_DISPLAY_CONTROL && $__wcf->user->userID && $__wcf->session->getPermission('user.bookmark.canUseBookmark')}
    <li class="menuOverlayItem" data-more="com.uz.wsc.bookmark">
        <a href="{link controller='User' object=$__wcf->user}{/link}#bookmark" class="menuOverlayItemLink menuOverlayItemBadge box24" data-badge-identifier="unreadBookmarks">
            <span class="icon icon24 fa-{BOOKMARK_DISPLAY_ICON}"></span>
            <span class="menuOverlayItemTitle">{lang}wcf.bookmark.bookmarks{/lang}</span>
            {if $__wcf->getBookmarkHandler()->getUnreadBookmarkCount()}<span class="badge badgeUpdate">{#$__wcf->getBookmarkHandler()->getUnreadBookmarkCount()}</span>{/if}
        </a>
    </li>
{/if}
