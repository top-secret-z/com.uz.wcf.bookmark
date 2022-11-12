{if BOOKMARK_SHARE_ENABLE}
    {if $shares|count}
        <li>{lang}wcf.bookmark.panel.shared{/lang}</li>
    {/if}

    {foreach from=$shares item=share}
        <li class="bookmarkItem{if $share->lastVisitTime < $share->time} interactiveDropdownItemOutstanding{/if}" data-link="{link controller='BookmarkSharedList' object=$share}action=firstNew{/link}" data-object-id="{@$share->shareID}" data-is-read="{if $share->lastVisitTime < $share->time}false{else}true{/if}">
            <div class="box48">
                <div>
                    {@$share->getUserProfile()->getAvatar()->getImageTag(48)}
                </div>
                <div>
                    <h3><a href="{link controller='BookmarkSharedList' object=$share}action=firstNew{/link}">{$share->remark|truncate:250}</a></h3>
                    <small class="bookmarkInfo">
                        <span class="bookmarkOwner">
                            {user object=$share->getUserProfile()}
                        </span>

                        <span class="bookmarkTime">{@$share->time|time}</span>
                    </small>
                </div>
            </div>
        </li>
    {/foreach}
{else}
    {foreach from=$shares item=share}
        <li class="bookmarkItem">
            <div class="box48">
                <div>
                    {@$share->getUserProfile()->getAvatar()->getImageTag(48)}
                </div>
                <div>
                    <h3><a href="{$share->getUrl()}">{@$share->getTitle()|truncate:250}</a></h3>
                    <small class="bookmarkInfo">
                        <span class="bookmarkOwner">
                            {lang}wcf.bookmark.type.{@$share->getObjectTypeName()}{/lang}
                        </span>

                        <span class="bookmarkTime">{@$share->time|time}</span>
                    </small>
                </div>
            </div>
        </li>
    {/foreach}
{/if}
