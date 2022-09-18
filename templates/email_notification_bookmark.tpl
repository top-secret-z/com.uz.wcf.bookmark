{assign var='bookmark' value=$event->getUserNotificationObject()->getBookmark()}

{if $mimeType === 'text/plain'}
    {lang}wcf.user.notification.bookmark.mail.plaintext{/lang}

    {@$bookmark->remark}
{else}
    {lang}wcf.user.notification.bookmark.mail.html{/lang}
    {assign var='user' value=$event->getAuthor()}

    {if $notificationType == 'instant'}
        {assign var='avatarSize' value=128}
    {else}
        {assign var='avatarSize' value=64}
    {/if}

    {capture assign='bookmarkContent'}
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><a href="{link controller='User' object=$user isHtmlEmail=true}{/link}" title="{$bookmark->username}">{@$user->getAvatar()->getImageTag($avatarSize)}</a></td>
                <td class="boxContent">
                    <div class="containerHeadline">
                        <h3>
                            {if $bookmark->userID}
                                <a href="{link controller='User' object=$user isHtmlEmail=true}{/link}">{$bookmark->username}</a>
                            {else}
                                {$bookmark->username}
                            {/if}
                            &#xb7;
                            <small>{$bookmark->time|plainTime}</small>
                        </h3>
                    </div>
                    <div>
                        {@$bookmark->remark}
                    </div>
                </td>
            </tr>
        </table>
    {/capture}
    {include file='email_paddingHelper' block=true class='box'|concat:$avatarSize content=$bookmarkContent sandbox=true}
{/if}
