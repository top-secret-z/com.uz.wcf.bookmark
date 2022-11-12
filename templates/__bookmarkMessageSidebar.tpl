{if MODULE_BOOKMARK && $__wcf->getSession()->getPermission('user.bookmark.canViewBookmark')}
    {if BOOKMARK_DISPLAY_MESSAGE_SIDEBAR_BOOKMARKS && $userProfile->bookmarks}
        <dt>{if $userProfile->isAccessible('canViewProfile')}<a href="{link controller='User' object=$userProfile}{/link}#bookmark" class="jsTooltip" title="{lang user=$userProfile}wcf.bookmark.bookmarks.show{/lang}">{lang}wcf.bookmark.bookmarks{/lang}</a>{else}{lang}wcf.bookmark.bookmarks{/lang}{/if}</dt>
        <dd>{#$userProfile->bookmarks}</dd>
    {/if}

    {if BOOKMARK_SHARE_ENABLE && BOOKMARK_DISPLAY_MESSAGE_SIDEBAR_BOOKMARKSHARES && $userProfile->bookmarkShares}
        <dt>{if $userProfile->isAccessible('canViewProfile')}<a href="{link controller='User' object=$userProfile}{/link}#bookmark" class="jsTooltip" title="{lang user=$userProfile}wcf.bookmark.bookmarks.show{/lang}">{lang}wcf.bookmark.shares{/lang}</a>{else}{lang}wcf.bookmark.shares{/lang}{/if}</dt>
        <dd>{#$userProfile->bookmarkShares}</dd>
    {/if}
{/if}
