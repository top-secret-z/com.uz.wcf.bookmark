{include file='header' pageTitle='wcf.acp.bookmark.share.list'}

<script data-relocate="true">
	$(function() {
		new WCF.Action.Delete('wcf\\data\\bookmark\\share\\BookmarkShareAction', '.jsShareRow');
		new WCF.Search.User('#username', null, false, [ ], true);
	});
</script>

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}wcf.acp.bookmark.share.list{/lang}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}{event name='contentHeaderNavigation'}{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{if $objects|count}
	<form method="post" action="{link controller='BookmarkShareList'}{/link}">
		<section class="section">
			<h2 class="sectionTitle">{lang}wcf.global.filter{/lang}</h2>
			
			<div class="row rowColGap formGrid">
				<dl class="col-xs-12 col-md-4">
					<dt></dt>
					<dd>
						<input type="text" id="username" name="username" value="{$username}" placeholder="{lang}wcf.user.username{/lang}" class="long">
					</dd>
				</dl>
			</div>
			
			<div class="formSubmit">
				<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
				{csrfToken}
			</div>
		</section>
	</form>
{/if}

{hascontent}
	<div class="paginationTop">
		{content}
			{assign var='linkParameters' value=''}
			{if $username}{capture append=linkParameters}&username={@$username|rawurlencode}{/capture}{/if}
			
			{pages print=true assign=pagesLinks controller="BookmarkShareList" link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder$linkParameters"}
		{/content}
	</div>
{/hascontent}

{if $objects|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnID columnShareID{if $sortField == 'shareID'} active {@$sortOrder}{/if}" colspan="2"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=shareID&sortOrder={if $sortField == 'shareID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
					<th class="columnText columnTime{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.time{/lang}</a></th>
					<th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.username{/lang}</a></th>
					<th class="columnText columnReceiverName{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.receiverName{/lang}</a></th>
					<th class="columnText columnShareWith{if $sortField == 'shareWith'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=shareWith&sortOrder={if $sortField == 'shareWith' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.shareWith{/lang}</a></th>
					<th class="columnText columnUrl{if $sortField == 'url'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=url&sortOrder={if $sortField == 'url' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.url{/lang}</a></th>
					<th class="columnText columnRemark{if $sortField == 'remark'} active {@$sortOrder}{/if}"><a href="{link controller='BookmarkShareList'}pageNo={@$pageNo}&sortField=remark&sortOrder={if $sortField == 'remark' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{@$linkParameters}{/link}">{lang}wcf.acp.bookmark.remark{/lang}</a></th>
					
					{event name='columnHeads'}
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$objects item=share}
					<tr class="jsShareRow">
						<td class="columnIcon">
							<span class="icon icon16 fa-times jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" data-object-id="{@$share->shareID}" data-confirm-message="{lang}wcf.acp.bookmark.share.delete.sure{/lang}"></span>
						</td>
						<td class="columnID columnShareID">{@$share->shareID}</td>
						<td class="columnText columnTime">{@$share->time|time}</td>
						<td class="columnText columnUsername">{$share->username}</td>
						<td class="columnText columnReceiverName">{$share->receiverName}</td>
						<td class="columnText columnShareWith">{@$share->shareWith}</td>
						<td class="columnText columnUrl">{$share->url}</td>
						<td class="columnText columnRemark">{$share->remark}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
	
	<footer class="contentFooter">
		{hascontent}
			<div class="paginationBottom">
				{content}{@$pagesLinks}{/content}
			</div>
		{/hascontent}
		
		{hascontent}
			<nav class="contentFooterNavigation">
				<ul>
					{content}{event name='contentFooterNavigation'}{/content}
				</ul>
			</nav>
		{/hascontent}
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

{include file='footer'}
