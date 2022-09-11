{if MODULE_BOOKMARK && $__wcf->user->userID && $__wcf->session->getPermission('user.bookmark.canUseBookmark')}
	<script data-relocate="true">
		require(['UZ/Bookmark/Add'], function (UZBookmarkAdd) {
			new UZBookmarkAdd('article', '{$article->getTitle()}', '{$article->getLink()}', '.jsAddBookmarkButton');
		});
	</script>
	<li class="jsOnly"><a href="#" data-object-id="{@$article->articleID}" class="jsAddBookmarkButton button jsTooltip" title="{lang}wcf.bookmark.add{/lang}"><span class="icon icon16 fa-{BOOKMARK_DISPLAY_ICON}"></span> <span class="invisible">{lang}wcf.bookmark.add{/lang}</span></a></li>
{/if}
