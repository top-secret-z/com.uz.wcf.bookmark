<ul class="sidebarItemList">
    {foreach from=$bookmarks item=bookmark}
        {assign var='user' value=$bookmark->getUserProfile()}

        <li class="box24">
            <a href="{link controller='User' object=$user}{/link}#bookmark">{@$user->getAvatar()->getImageTag(24)}</a>
            <div class="sidebarItemTitle">
                <h3><a href="{$bookmark->getUrl()}">{$bookmark->getTitle()|truncate:75}</a></h3>
                <small>
                    {if $bookmark->userID}
                        {user object=$user}
                    {else}
                        {$bookmark->username}
                    {/if}
                    <span class="separatorLeft">{@$bookmark->time|time}</span>
                </small>
            </div>
        </li>
    {/foreach}
</ul>
