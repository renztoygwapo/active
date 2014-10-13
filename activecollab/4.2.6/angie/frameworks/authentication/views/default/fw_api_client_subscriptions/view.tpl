{title lang=false}API Subscription Details{/title}
{add_bread_crumb}API Subscription Details{/add_bread_crumb}

{use_widget name=properties_list module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="api_client_subscription_details" class="object_inspector">
  <dl class="properties_list">
    <dt>{lang}Client{/lang}</dt>
    <dd>{$active_api_client_subscription->getClientName()}</dd>

    <dt>{lang}Client Vendor{/lang}</dt>
    <dd>
    {if $active_api_client_subscription->getClientVendor()}
      {$active_api_client_subscription->getClientVendor()}
    {else}
      <span class="details">{lang}Unknown{/lang}</span>
    {/if}
    </dd>

    <dt>{lang}Enabled{/lang}</dt>
    <dd>
    {if $active_api_client_subscription->getIsEnabled()}
      {lang}Yes{/lang}
    {else}
      {lang}No{/lang}
    {/if}
    </dd>

  {if $active_api_client_subscription->getIsEnabled()}
    <dt>{lang}Access Level{/lang}</dt>
    <dd>
    {if $active_api_client_subscription->getIsReadOnly()}
      {lang}Read Only{/lang}
    {else}
      {lang}Read and Write{/lang}
    {/if}
    </dd>
  {/if}

    <dt>{lang}Created On{/lang}</dt>
    <dd>{$active_api_client_subscription->getCreatedOn()|datetime}</dd>

    <dt>{lang}Last Used On{/lang}</dt>
    <dd>
    {if $active_api_client_subscription->getLastUsedOn()}
      {$active_api_client_subscription->getLastUsedOn()|datetime}
    {else}
      <span class="details">{lang}Never Used{/lang}</span>
    {/if}
    </dd>
  </dl>
  
  <div class="body">
    <p>{lang url=$active_api_client_subscription->getApiUrl()}API URL: <span class="token">:url</span>{/lang}</p>
    <p>{lang token=$active_api_client_subscription->getFormattedToken()}Token: <span class="token">:token</span>{/lang}</p>
  </div>
</div>