<dl class="jsShareBookmark">
	<dt><label for="bookmarkReceiverInput">{lang}wcf.bookmark.share.receiver{/lang}</label></dt>
	<dd>
		<textarea id="bookmarkReceiverInput" name="bookmarkReceivers" class="long" cols="40" rows="2"></textarea>
		<small>{lang}wcf.bookmark.share.receiver.description{/lang}</small>
	</dd>
</dl>

<dl>
	<dt><label for="bookmarkReceiverInput">{lang}wcf.bookmark.share.remark{/lang}</label></dt>
	<dd>
		<textarea id="remark" name="remark" class="long jsBookmarkRemark" cols="40" rows="3">{$remark}</textarea>
		<small>{lang}wcf.bookmark.share.remark.description{/lang}</small>
	</dd>
</dl>

<div class="formSubmit">
	<button id="shareBookmark" class="buttonPrimary">{lang}wcf.global.button.submit{/lang}</button>
</div>