{title}Update Currency{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="edit_currency">
  {form action=$active_currency->getEditUrl() method=post}
  	{wrap_fields}
    	{include file=get_view_path('_currency_form', 'fw_currencies_admin', $smarty.const.GLOBALIZATION_FRAMEWORK)}
    {/wrap_fields}  
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>