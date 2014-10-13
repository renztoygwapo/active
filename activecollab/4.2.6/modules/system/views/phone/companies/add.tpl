{title}New Company{/title}
{add_bread_crumb}New Company{/add_bread_crumb}

<div id="new_company">
	{form action=Router::assemble('people_companies_add')}
	  {include file=get_view_path('_company_form', 'companies', $smarty.const.SYSTEM_MODULE)}

  {if AngieApplication::behaviour()->isTrackingEnabled()}
    <input type="hidden" name="_intent_id" value="{AngieApplication::behaviour()->recordIntent('company_created', 'mobile')}">
  {/if}
	
	  {wrap_buttons}
	    {submit}Add Company{/submit}
	  {/wrap_buttons}
	{/form}
</div>