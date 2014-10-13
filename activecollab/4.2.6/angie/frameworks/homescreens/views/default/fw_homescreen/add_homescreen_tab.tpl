<div id="add_homescreen_tab">
  {form action=$active_object->homescreen()->getAddTabUrl()}
  	{wrap_fields}
    	{include file=get_view_path('_homescreen_tab_form', 'fw_homescreen', $smarty.const.HOMESCREENS_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Add Homescreen Tab{/submit}
    {/wrap_buttons}
  {/form}
</div>