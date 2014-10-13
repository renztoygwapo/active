<div id="edit_homescreen_tab">
  {form action=$active_homescreen_tab->getEditUrl()}
  	{wrap_fields}
    	{include file=get_view_path('_homescreen_tab_form', 'fw_homescreen', $smarty.const.HOMESCREENS_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>