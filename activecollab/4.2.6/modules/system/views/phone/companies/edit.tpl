{title}Update Information{/title}
{add_bread_crumb}Update Information{/add_bread_crumb}

<div id="update_company">
	{form action=$active_company->getEditUrl()}
	  {include file=get_view_path('_company_form', 'companies', $smarty.const.SYSTEM_MODULE)}
	  
	  {wrap_buttons}
	    {submit}Save Changes{/submit}
	  {/wrap_buttons}
	{/form}
</div>