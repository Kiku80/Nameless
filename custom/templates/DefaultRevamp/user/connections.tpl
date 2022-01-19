{include file='header.tpl'}
{include file='navbar.tpl'}

<h2 class="ui header">
  {$TITLE}
</h2>

<div class="ui stackable grid" id="user">
  <div class="ui centered row">
    <div class="ui six wide tablet four wide computer column">
	  {include file='user/navigation.tpl'}
    </div>
    <div class="ui ten wide tablet twelve wide computer column">
      <div class="ui segment">
        <h3 class="ui header">{$CONNECTIONS}</h3>

        {foreach from=$INTEGRATIONS item=integration}
          <div class="ui segments">
           <div class="ui segment">
              <h3 class="ui header" style="display:inline">{$integration.name}</h3> <div class="res right floated"><a type="submit" class="ui mini positive button">Connect</a></div>
            </div>
            <div class="ui {if $integration.connected}green{else}red{/if} segment">
              {if $integration.connected}
                {$integration.username}
              {else}
                Not Connected
              {/if}
            </div>
          </div>
        {/foreach}

      </div>
    </div>
  </div>
</div>

{include file='footer.tpl'}