{if $alreadyBookmarked}
	<p class="info">{lang}wcf.bookmark.exist{/lang}</p>
{/if}

<section class="section sectionBookmarkTitle">
	<h2 class="sectionTitle">{lang}wcf.bookmark.title{/lang}</h2>
	<dl>
		<dt></dt>
			<input type="text" id="title" name="title" value="{$title}" class="long jsBookmarkTitle">
		</dd>
	</dl>
</section>

<section class="section">
	<h2 class="sectionTitle">{lang}wcf.bookmark.access{/lang}</h2>
	<dl>
		<dt></dt>
		<dd class="floated">
			<label><input type="radio" name="access" id="access0" value="0" {if $isPrivate == 0} checked="checked"{/if}> {lang}wcf.bookmark.access.public{/lang}</label>
			<label><input type="radio" name="access" id="access1" value="1" {if $isPrivate == 1} checked="checked"{/if}> {lang}wcf.bookmark.access.private{/lang}</label>
			<label><input type="radio" name="access" id="access2" value="2" {if $isPrivate == 2} checked="checked"{/if}> {lang}wcf.bookmark.access.follower{/lang}</label>
			
		</dd>
	</dl>
</section>

<section class="section sectionBookmarkUrl">
	<header class="sectionHeader">
		<h2 class="sectionTitle">{lang}wcf.bookmark.url{/lang}</h2>
		{if $action == 'add'}
			<p class="sectionDescription">{lang}wcf.bookmark.url.description{/lang}</p>
		{else}
			<p class="sectionDescription">{lang}wcf.bookmark.url.description.edit{/lang}</p>
		{/if}
	</header>
	
	<dl>
		<dt></dt>
		<dd>
			<textarea id="url" name="url" class="long jsBookmarkUrl" {if $action|isset && $action == 'edit'}disabled{/if} cols="40" rows="2">{$url}</textarea>
		</dd>
	</dl>
</section>

<section class="section sectionBookmarkRemark">
	<h2 class="sectionTitle">{lang}wcf.bookmark.remark{/lang}</h2>
	<dl>
		<dt></dt>
		<dd>
			<textarea id="remark" name="remark" class="long jsBookmarkRemark" cols="40" rows="3">{$remark}</textarea>
		</dd>
	</dl>
</section>

{if $action|isset && $action == 'edit' && $shares}
	<section class="section sectionBookmarkRemark">
		<h2 class="sectionTitle">{lang}wcf.bookmark.share.with{/lang}</h2>
		<p>{@$shares}</p>
	</section>
{/if}

<div class="formSubmit">
	<button id="jsSubmitBookmark" class="jsSubmitBookmark buttonPrimary">{lang}wcf.global.button.submit{/lang}</button>
</div>