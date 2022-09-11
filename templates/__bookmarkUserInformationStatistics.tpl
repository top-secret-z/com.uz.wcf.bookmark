{if MODULE_BOOKMARK && $__wcf->getSession()->getPermission('user.bookmark.canViewBookmark')}
	{if BOOKMARK_DISPLAY_USERINFORMATION_BOOKMARKS && $user->bookmarks}
		<dt>{if $user->isAccessible('canViewProfile')}<a href="{link controller='User' object=$user}{/link}#bookmark" class="jsTooltip" title="{lang}wcf.bookmark.bookmarks.show{/lang}">{lang}wcf.bookmark.bookmarks{/lang}</a>{else}{lang}wcf.bookmark.bookmarks{/lang}{/if}</dt>
		<dd>{#$user->bookmarks}</dd>
	{/if}
	
	{if BOOKMARK_SHARE_ENABLE && BOOKMARK_DISPLAY_USERINFORMATION_BOOKMARKSHARES && $user->bookmarkShares}
		<dt>{if $user->isAccessible('canViewProfile')}<a href="{link controller='User' object=$user}{/link}#bookmark" class="jsTooltip" title="{lang}wcf.bookmark.bookmarks.show{/lang}">{lang}wcf.bookmark.shares{/lang}</a>{else}{lang}wcf.bookmark.shares{/lang}{/if}</dt>
		<dd>{#$user->bookmarkShares}</dd>
	{/if}
{/if}
