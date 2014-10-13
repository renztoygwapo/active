{title}Update Information{/title}
{add_bread_crumb}Update Information{/add_bread_crumb}

{form action=$active_company->getEditUrl() method=post}
  {include file=get_view_path('_company_form', 'companies', 'system')}
  
  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}
