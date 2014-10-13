{title}New Subscription{/title}

<div id="add_api_client_subscription">
  {form action=$active_object->getAddApiSubscriptionUrl() csfr_protect=true}
    {wrap_fields}
    	{include file=get_view_path('_api_client_subscription_form', 'fw_api_client_subscriptions', $smarty.const.AUTHENTICATION_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Add Subscription{/submit}
    {/wrap_buttons}
  {/form}
</div>