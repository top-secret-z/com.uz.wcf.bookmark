{if MODULE_BOOKMARK && $__wcf->user->userID && $__wcf->session->getPermission('user.bookmark.canUseBookmark') && $__wcf->user->userID != $user->userID}
	<script data-relocate="true">
		require(['UZ/Bookmark/Add'], function (UZBookmarkAdd) {
			new UZBookmarkAdd('user', '{$user->username}', '{$user->getLink()}', '.jsAddBookmarkUser');
		});
	</script>
	
	{if WCF_VERSION|substr:0:3 >= '5.5'}
		<li class="jsOnly"><a href="#" data-object-id="{@$user->userID}" class="jsAddBookmarkUser button jsTooltip" title="{lang}wcf.bookmark.add{/lang}"><span class="icon icon16 fa-{BOOKMARK_DISPLAY_ICON}"></span> <span class="invisible">{lang}wcf.bookmark.add{/lang}</span></a></li>
	{else}
		<li class="jsOnly"><a href="#" data-object-id="{@$user->userID}" class="jsAddBookmarkUser button jsTooltip" title="{lang}wcf.bookmark.add{/lang}"><span class="icon icon32 fa-{BOOKMARK_DISPLAY_ICON}"></span> <span class="invisible">{lang}wcf.bookmark.add{/lang}</span></a></li>
	{/if}
{/if}
