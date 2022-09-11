{if $bookmarkList|count}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Bookmark{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	<script data-relocate="true">
		$(function() {
			new WCF.Bookmark.Loader({@$userID});
			new WCF.Bookmark.InlineEditor('.bookmark');
		});
	</script>
	
	<ul id="bookmarkList" class="containerList recentActivityList bookmarkList" data-last-bookmark-time="{@$lastBookmarkTime}" data-share-enable="{@$shareEnable}">
		<li class="containerListButtonGroup buttonSelection">
			{if $__wcf->user->userID == $userID || $__wcf->getSession()->getPermission('mod.bookmark.canModerateBookmark')}
				<ul class="buttonGroup" id="bookmarkAccess">
					<li><a class="button small active" data-bookmark-access="all">{lang}wcf.bookmark.access.all{/lang}</a></li>
					<li><a class="button small" data-bookmark-access="public">{lang}wcf.bookmark.access.public{/lang}</a></li>
					<li><a class="button small" data-bookmark-access="private">{lang}wcf.bookmark.access.private{/lang}</a></li>
					<li><a class="button small" data-bookmark-access="follower">{lang}wcf.bookmark.access.follower{/lang}</a></li>
				</ul>
			{/if}
			
			<ul class="buttonGroup">
				<li class="dropdown">
					<a class="dropdownToggle button small"><span id="bookmarkTypeSelector">{lang}wcf.bookmark.type.all{/lang}</span></a>
					<ul class="dropdownMenu" id="bookmarkType">
						<li id="all"><span>{lang}wcf.bookmark.type.all{/lang}</span></li>
						<li class="dropdownDivider"></li>
						<li id="external"><span>{lang}wcf.bookmark.type.external{/lang}</span></li>
						<li class="dropdownDivider"></li>
						<li id="article"><span>{lang}wcf.bookmark.type.article{/lang}</span></li>
						<li id="user"><span>{lang}wcf.bookmark.type.user{/lang}</span></li>
						
						{event name='dropDownType'}
						
						<li class="dropdownDivider"></li>
					</ul>
				</li>
			</ul>
		</li>
		
		{include file='userProfileBookmarkItem'}
	</ul>
	
{else}
	<div class="section">
		{lang}wcf.bookmark.profile.noItems{/lang}
	</div>
{/if}
