{foreach from=$bookmarkList item=bookmark}
    <li class="bookmark" id="bookmark{@$bookmark->bookmarkID}" data-bookmark-id="{@$bookmark->bookmarkID}">
        <div class="box48">
            <a href="{link controller='User' object=$bookmark->getUserProfile()}{/link}" title="{$bookmark->getUserProfile()->username}">{@$bookmark->getUserProfile()->getAvatar()->getImageTag(48)}</a>

            <div>
                <div class="containerHeadline">
                    <h3 style="width:90%;">
                        {user object=$bookmark->getUserProfile()}
                        <small class="separatorLeft">{@$bookmark->time|time}</small>
                        {if BOOKMARK_SHARE_ENABLE}
                            {if $bookmark->shareFrom}
                                <small class="separatorLeft">{lang}wcf.bookmark.share.from{/lang} {$bookmark->shareFrom}</small>
                            {/if}
                            {if $bookmark->shareWith}
                                <small class="separatorLeft" id="shareWith{$bookmark->bookmarkID}">{lang}wcf.bookmark.share.with{/lang} {@$bookmark->shareWith}</small>
                            {else}
                                <small class="separatorLeft" id="shareWith{$bookmark->bookmarkID}">{lang}wcf.bookmark.share.with.not{/lang}</small>
                            {/if}
                        {/if}

                        {if $__wcf->user->userID == $bookmark->userID || $__wcf->session->getPermission('mod.bookmark.canModerateBookmark')}
                            <small class="separatorLeft"><a class="jsBookmarkEditor pointer" data-bookmark-id="{@$bookmark->bookmarkID}"> {lang}wcf.global.button.edit{/lang}</a></small>
                        {/if}
                    </h3>
                    <div class="bookmarkProfileTitle">
                        {if !$bookmark->isExternal}
                            <a href='{$bookmark->getUrl()}'><span id="title{@$bookmark->bookmarkID}">{@$bookmark->getTitle()}</span></a>
                        {else}
                            <a href="{$bookmark->getUrl()}"{if EXTERNAL_LINK_REL_NOFOLLOW || EXTERNAL_LINK_TARGET_BLANK} rel="{if EXTERNAL_LINK_REL_NOFOLLOW}nofollow{/if}{if EXTERNAL_LINK_TARGET_BLANK}{if EXTERNAL_LINK_REL_NOFOLLOW} {/if}noopener noreferrer{/if}"{/if}{if EXTERNAL_LINK_TARGET_BLANK} target="_blank"{/if}><span id="title{@$bookmark->bookmarkID}">{@$bookmark->getTitle()}</span></a>
                        {/if}
                    </div>
                    <small class="containerContentType">{lang}wcf.bookmark.type.{@$bookmark->getObjectTypeName()}{/lang}</small>
                </div>

                <div id="remark{@$bookmark->bookmarkID}" class="containerContent">{@$bookmark->getRemark()}</div>
            </div>

        </div>
    </li>
{/foreach}
