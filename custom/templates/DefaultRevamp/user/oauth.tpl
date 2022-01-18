{include file='header.tpl'}
{include file='navbar.tpl'}

<h2 class="ui header">
    {$USER_CP}
</h2>

<div class="ui stackable grid" id="alerts">
    <div class="ui centered row">
        <div class="ui six wide tablet four wide computer column">
            {include file='user/navigation.tpl'}
        </div>
        <div class="ui ten wide tablet twelve wide computer column">
            <div class="ui segment">
                <h3 class="ui header">
                    OAuth
                </h3>
                {if isset($SUCCESS_MESSAGE)}
                    <div class="ui success icon message">
                        <i class="check icon"></i>
                        <div class="content">
                            <div class="header">{$SUCCESS}</div>
                            {$SUCCESS_MESSAGE}
                        </div>
                    </div>
                {/if}
                <div class="ui middle aligned relaxed selection list">
                    {nocache}
                        <table class="ui striped table">
                            <tbody>
                            {foreach $OAUTH_PROVIDERS as $provider_name => $provider_data}
                                <tr>
                                    <td>
                                        <div class="ui stackable middle aligned grid">
                                            <div class="row">
                                                <div class="ten wide column">
                                                    {if $provider_data.icon}
                                                        <i class="{$provider_data.icon} fa-lg">&nbsp;</i>
                                                    {/if}
                                                    {$provider_name|ucfirst}
                                                </div>
                                                <div class="four wide column">
                                                    {if isset($USER_OAUTH_PROVIDERS[$provider_name])}
                                                        <div class="res right floated">
                                                            <code>{$USER_OAUTH_PROVIDERS[$provider_name]->provider_id}</code>
                                                        </div>
                                                    {/if}
                                                </div>
                                                <div class="two wide column right aligned">
                                                    {if isset($USER_OAUTH_PROVIDERS[$provider_name])}
                                                        <a class="ui mini red button" href="#" data-toggle="modal" data-target="#modal-unlink-{$provider_name}">Unlink</a>
                                                    {else}
                                                        <a class="ui mini green button" href="#" data-toggle="modal" data-target="#modal-link-{$provider_name}">Link</a>
                                                    {/if}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    {/nocache}
                </div>
            </div>
        </div>
    </div>
</div>

{foreach $OAUTH_PROVIDERS as $provider_name => $provider_data}
    <div class="ui small modal" id="modal-unlink-{$provider_name}">
        <div class="header">
            Unlink {$provider_name|ucfirst}
        </div>
        <div class="content">
            Are you sure you want to unlink your account from {$provider_name|ucfirst}?
            You will not be able to login with this account anymore.
        </div>
        <div class="actions">
            <a class="ui negative button">{$NO}</a>
            <form class="ui form" action="" method="post" style="display: inline">
                <input type="hidden" name="token" value="{$TOKEN}">
                <input type="hidden" name="action" value="unlink">
                <input type="hidden" name="provider" value="{$provider_name}">
                <input type="submit" class="ui green button" value="{$YES}">
            </form>
        </div>
    </div>

    <div class="ui small modal" id="modal-link-{$provider_name}">
        <div class="header">
            Link {$provider_name|ucfirst}
        </div>
        <div class="content">
            You will be taken to the {$provider_name|ucfirst} website to link your account.
        </div>
        <div class="actions">
            <a class="ui negative button">{$NO}</a>
            <a class="ui green button" href="{$provider_data.url}">{$CONFIRM}</a>
        </div>
    </div>
{/foreach}

{include file='footer.tpl'}
