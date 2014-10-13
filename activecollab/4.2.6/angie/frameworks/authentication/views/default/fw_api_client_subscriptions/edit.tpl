{title}Update Subscription{/title}
{add_bread_crumb}Update Subscription{/add_bread_crumb}

<div id="edit_api_client_subscription">
  {form action=$active_api_client_subscription->getEditUrl() csfr_protect=true}
  	{wrap_fields}
    	{include file=get_view_path('_api_client_subscription_form', 'fw_api_client_subscriptions', $smarty.const.AUTHENTICATION_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>