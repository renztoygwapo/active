{title}New Language{/title}
{add_bread_crumb}New Language{/add_bread_crumb}

<div id="add_language">
  {form action=Router::assemble('admin_languages_add') method=post}
    {wrap_fields}
    	{include file=get_view_path('_language_form', 'fw_languages_admin', $smarty.const.GLOBALIZATION_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Add Language{/submit}
    {/wrap_buttons}
  {/form}
</div>