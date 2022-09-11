{capture assign='pageTitle'}{$__wcf->getActivePage()->getTitle()}{if $pageNo > 1} - {lang}wcf.page.pageNo{/lang}{/if}{/capture}

{capture assign='contentTitle'}{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$items}</span>{/capture}

{capture assign='headContent'}
	{if $pageNo < $pages}
		<link rel="next" href="{link controller='UserBookmarkList'}pageNo={@$pageNo+1}{/link}">
	{/if}
	{if $pageNo > 1}
		<link rel="prev" href="{link controller='UserBookmarkList'}{if $pageNo > 2}pageNo={@$pageNo-1}&{/if}{/link}">
	{/if}
{/capture}

{assign var='linkParameters' value=''}
{if $search}{capture append=linkParameters}&search={@$search|rawurlencode}{/capture}{/if}
{if $type}{capture append=linkParameters}&type={@$type|rawurlencode}{/capture}{/if}
{if $isPrivate}{capture append=linkParameters}&isPrivate={@$isPrivate|rawurlencode}{/capture}{/if}

{if WCF_VERSION|substr:0:3 >= '5.5'}
	{capture assign='contentHeaderNavigation'}
		<li class="jsOnly"><a href="#" class="jsAddBookmarkMenu button buttonPrimary"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.bookmark.add{/lang}</span></a></li>
	{/capture}
	
	{capture assign='contentInteractionPagination'}
		{pages print=true assign=pagesLinks controller='UserBookmarkList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
	{/capture}
	
	{include file='header'}
{else}
	{capture assign='contentHeaderNavigation'}
		<li class="jsOnly"><a href="#" class="jsAddBookmarkMenu button"><span class="icon icon16 fa-plus"></span> <span>{lang}wcf.bookmark.add{/lang}</span></a></li>
	{/capture}
	
	{include file='header'}
	
	{hascontent}
		<div class="paginationTop">
			{content}
				{pages print=true assign=pagesLinks controller="UserBookmarkList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
			{/content}
		</div>
	{/hascontent}
{/if}

<form method="post" action="{link controller='UserBookmarkList'}{/link}">
	<section class="section">
		<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
		
		<div class="row rowColGap formGrid">
			<dl class="col-xs-12 col-md-3">
				<dt></dt>
				<dd>
					<input type="text" id="search" name="search" value="{$search}" placeholder="{lang}wcf.bookmark.search{/lang}" class="long">
				</dd>
			</dl>
			
			<dl class="col-xs-12 col-md-3">
				<dt></dt>
				<dd>
					<select name="type" id="type">
						<option value="">{lang}wcf.bookmark.type.all{/lang}</option>
						{htmlOptions options=$availableTypes selected=$type}
					</select>
				</dd>
			</dl>
			
			<dl class="col-xs-12 col-md-3">
				<dt></dt>
				<dd>
					<select name="isPrivate" id="isPrivate">
						<option value="0"{if $isPrivate == 0} selected="selected"{/if}>{lang}wcf.bookmark.access.public{/lang}</option>
						<option value="1"{if $isPrivate == 1} selected="selected"{/if}>{lang}wcf.bookmark.access.private{/lang}</option>
						<option value="2"{if $isPrivate == 2} selected="selected"{/if}>{lang}wcf.bookmark.access.follower{/lang}</option>
					</select>
				</dd>
			</dl>
		</div>
		
		<div class="formSubmit">
			<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
			{csrfToken}
		</div>
	</section>
</form>

{if $objects|count}
	<script data-relocate="true" src="{@$__wcf->getPath()}js/WCF.Bookmark{if !ENABLE_DEBUG_MODE}.min{/if}.js?v={@LAST_UPDATE_TIME}"></script>
	<script data-relocate="true">
		$(function() {
			new WCF.Bookmark.InlineEditor('.bookmark');
		});
	</script>
	
	<div class="section tabularBox">
		<table id="bookmarkList" class="table bookmarkList" data-share-enable="{@$shareEnable}">
			<thead>
				<tr>
					<th class="columnTime columnText{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='UserBookmarkList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.bookmark.time{/lang}</a></th>
					<th class="columnTitle columnText{if $sortField == 'title'} active {@$sortOrder}{/if}"><a href="{link controller='UserBookmarkList'}pageNo={@$pageNo}&sortField=title&sortOrder={if $sortField == 'title' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.bookmark.title{/lang}</a></th>
					<th class="columnType columnText{if $sortField == 'type'} active {@$sortOrder}{/if}"><a href="{link controller='UserBookmarkList'}pageNo={@$pageNo}&sortField=type&sortOrder={if $sortField == 'type' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.bookmark.type{/lang}</a>
					<th class="columnRemark columnText{if $sortField == 'remark'} active {@$sortOrder}{/if}"><a href="{link controller='UserBookmarkList'}pageNo={@$pageNo}&sortField=remark&sortOrder={if $sortField == 'remark' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.bookmark.remark{/lang}</a>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=bookmark}
					<tr class="bookmark" id="bookmark{@$bookmark->bookmarkID}" data-bookmark-id="{@$bookmark->bookmarkID}">
						<td class="columnTime columnText">
							{@$bookmark->time|time}<br>
							<small><a class="jsBookmarkEditor pointer"> {lang}wcf.global.button.edit{/lang}</a></small>
						</td>
						<td class="columnTitle columnText">
							{if !$bookmark->isExternal}
								<a href='{$bookmark->getUrl()}'><span id="title{@$bookmark->bookmarkID}">{@$bookmark->getTitle()}</span></a>
							{else}
								<a href="{$bookmark->getUrl()}"{if EXTERNAL_LINK_REL_NOFOLLOW || EXTERNAL_LINK_TARGET_BLANK} rel="{if EXTERNAL_LINK_REL_NOFOLLOW}nofollow{/if}{if EXTERNAL_LINK_TARGET_BLANK}{if EXTERNAL_LINK_REL_NOFOLLOW} {/if}noopener noreferrer{/if}"{/if}{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}><span id="title{@$bookmark->bookmarkID}">{@$bookmark->getTitle()}</span></a>
							{/if}
						</td>
						<td class="columnType columnText">{lang}wcf.bookmark.type.{@$bookmark->getObjectTypeName()}{/lang}</td>
						<td class="columnRemark columnText" id="remark{@$bookmark->bookmarkID}">{$bookmark->remark}</td>
						
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
{else}
	<p class="info">{lang}wcf.bookmark.noEntries{/lang}</p>
{/if}

<footer class="contentFooter">
	{hascontent}
		<div class="paginationBottom">
			{content}{@$pagesLinks}{/content}
		</div>
	{/hascontent}
	
	{hascontent}
		<nav class="contentFooterNavigation">
			<ul>
				{content}
					
					{event name='contentFooterNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</footer>

<script data-relocate="true">
	require(['UZ/Bookmark/Add'], function (UZBookmarkAdd) {
		new UZBookmarkAdd('', '', '', '.jsAddBookmarkMenu');
	});
</script>

{include file='footer'}
