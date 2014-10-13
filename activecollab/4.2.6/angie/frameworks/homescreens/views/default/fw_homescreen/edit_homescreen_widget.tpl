<div id="edit_homescreen_widget">
  {form action=$active_homescreen_widget->getEditUrl()}
    {wrap_fields}
    	{$active_homescreen_widget->renderOptions($logged_user) nofilter}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>