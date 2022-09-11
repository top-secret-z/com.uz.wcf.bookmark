<ul class="sidebarItemList">
	{foreach from=$users item=user}
		<li class="box24">
			<a href="{link controller='User' object=$user}{/link}#bookmark" class="framed">{@$user->getAvatar()->getImageTag(24)}</a>
			
			<div class="sidebarItemTitle">
				<h3>{user object=$user}</h3>
				<small>{lang}wcf.bookmark.box.top{/lang}</small>
			</div>
		</li>
	{/foreach}
</ul>