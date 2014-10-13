{title}Update{/title}
{add_bread_crumb}Update{/add_bread_crumb}

<div id="update_language">
  {form action=$active_language->getEditUrl() method=post}
  	{wrap_fields}
    	{include file=get_view_path('_language_form', 'fw_languages_admin', $smarty.const.GLOBALIZATION_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>