{capture assign='contentHeader'}
    <header class="contentHeader">
        <div class="contentHeaderTitle">
            <h1 class="contentTitle">{$__wcf->getActivePage()->getTitle()} <span class="badge">{#$__wcf->getBookmarkHandler()->getTotalBookmarkShares(null, true)}</span></h1>
            <p class="contentDescription">{lang}wcf.user.menu.community.bookmarkShared{/lang}</p>
        </div>

        {hascontent}
            <nav class="contentHeaderNavigation">
                <ul>
                    {content}

                        {event name='contentHeaderNavigation'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </header>
{/capture}

{include file='userMenuSidebar'}

{include file='header'}

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks controller='BookmarkSharedList' link="pageNo=%d"}{/content}
    </div>
{/hascontent}

{if $items}
    {assign var=lastPeriod value=''}

    {foreach from=$objects item=$share}
        {if $share->getPeriod() != $lastPeriod}
            {if $lastPeriod}
                    </ul>
                </section>
            {/if}
            {assign var=lastPeriod value=$share->getPeriod()}
            {assign var=user value=$share->getUserProfile()}

            <section class="section sectionContainerList">
                <h2 class="sectionTitle">{$lastPeriod}</h2>

                <ul class="containerList">
        {/if}
                <li class="jsBookmarkItem bookmarkItem">
                    <div class="box48">
                        <a href="{link controller='User' object=$user}{/link}" title="{$user->username}">{@$user->getAvatar()->getImageTag(48)}</a>

                        <div id="divID{$share->shareID}" class="details">
                            {if $share->accepted}
                                <span class="badge label green">{lang}wcf.bookmark.share.accepted{/lang}</span>
                            {elseif $share->refused}
                                <span class="badge label red">{lang}wcf.bookmark.share.refused{/lang}</span>
                            {else}
                                <span class="badge label newContentBadge">{lang}wcf.message.new{/lang}</span>
                            {/if}

                            {user object=$user}
                            <small class="separatorLeft">{@$share->time|time}</small>
                            <br>
                            {$share->getBookmark()->getTitle()|truncate:50}
                            <br>
                            {@$share->remark}

                            <nav class="jsMobileNavigation buttonGroupNavigation">
                                <ul class="buttonList iconList jsOnly">
                                    <li><a class="pointer jsTooltip jsBookmarkViewButton" title="{lang}wcf.bookmark.button.view{/lang}" data-object-id="{@$share->shareID}"><span class="icon icon16 fa-search"></span> <span class="invisible">{lang}wcf.bookmark.button.view{/lang}</span></a></li>
                                    {if !$share->accepted && !$share->refused}
                                        <li id="accept{$share->shareID}"><a class="pointer jsTooltip jsBookmarkAcceptButton" title="{lang}wcf.bookmark.button.accept{/lang}" data-object-id="{@$share->shareID}"><span class="icon icon16 fa-check"></span> <span class="invisible">{lang}wcf.bookmark.button.accept{/lang}</span></a></li>
                                        <li id="refuse{$share->shareID}"><a class="pointer jsTooltip jsBookmarkRefuseButton" title="{lang}wcf.bookmark.button.refuse{/lang}" data-object-id="{@$share->shareID}"><span class="icon icon16 fa-times"></span> <span class="invisible">{lang}wcf.bookmark.button.refuse{/lang}</span></a></li>
                                    {/if}
                                </ul>
                            </nav>

                        </div>
                    </div>
                </li>
    {/foreach}
        </ul>
    </section>

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
    <p class="info">{lang}wcf.bookmark.share.none{/lang}</p>
{/if}

<script data-relocate="true">
    require(['UZ/Bookmark/View'], function (UZBookmarkView) {
        new UZBookmarkView();
    });

    require(['UZ/Bookmark/Refuse'], function (UZBookmarkRefuse) {
        new UZBookmarkRefuse();
    });

    require(['UZ/Bookmark/Accept'], function (UZBookmarkAccept) {
        new UZBookmarkAccept();
    });

    $(function() {
        WCF.Language.addObject({
            'wcf.bookmark.share.accepted':             '{jslang}wcf.bookmark.share.accepted{/jslang}',
            'wcf.bookmark.share.accept.confirm':     '{jslang}wcf.bookmark.share.accept.confirm{/jslang}',
            'wcf.bookmark.share.refused':             '{jslang}wcf.bookmark.share.refused{/jslang}',
            'wcf.bookmark.share.refuse.confirm':     '{jslang}wcf.bookmark.share.refuse.confirm{/jslang}'
        });
    });
</script>

{include file='footer'}
