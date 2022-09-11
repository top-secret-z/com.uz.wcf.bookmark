{if MODULE_BOOKMARK && BOOKMARK_DISPLAY_CONTROL && $__wcf->user->userID && $__wcf->session->getPermission('user.bookmark.canUseBookmark')}
	<li id="unreadBookmarks" data-count="{#$__wcf->getBookmarkHandler()->getUnreadBookmarkCount()}">
		<a class="jsTooltip" href="{link controller='User' object=$__wcf->user}{/link}#bookmark" title="{lang}wcf.bookmark.bookmarks{/lang}"><span class="icon icon32 fa-{BOOKMARK_DISPLAY_ICON}"></span> <span>{lang}wcf.bookmark.bookmarks{/lang}</span> {if $__wcf->getBookmarkHandler()->getUnreadBookmarkCount()}<span class="badge badgeUpdate">{#$__wcf->getBookmarkHandler()->getUnreadBookmarkCount()}</span>{/if}</a>
		{if !OFFLINE || $__wcf->session->getPermission('admin.general.canViewPageDuringOfflineMode')}
			<script data-relocate="true">
				$(function() {
					new WCF.Bookmark.UserPanel({
						newBookmark: '{lang}wcf.bookmark.add{/lang}',
						noItems: '{if BOOKMARK_SHARE_ENABLE}{lang}wcf.bookmark.share.none{/lang}{else}{lang}wcf.bookmark.none{/lang}{/if}',
						showAllLink: '{link controller='User' object=$__wcf->user}#bookmark{/link}',
						title: '{lang}wcf.bookmark.bookmarks{/lang}'
					});
				});
			</script>
		{/if}
	</li>
{/if}