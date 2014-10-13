{title}New Currency{/title}
{add_bread_crumb}New{/add_bread_crumb}

<div id="add_currency">
  {form action=Router::assemble('admin_currencies_add')}
    {wrap_fields}
    	{include file=get_view_path('_currency_form', 'fw_currencies_admin', $smarty.const.GLOBALIZATION_FRAMEWORK)}
    {/wrap_fields}  
    
    {wrap_buttons}
      {submit}Add Currency{/submit}
    {/wrap_buttons}
  {/form}
</div>