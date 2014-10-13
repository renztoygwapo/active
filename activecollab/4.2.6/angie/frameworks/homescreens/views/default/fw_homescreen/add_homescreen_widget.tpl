<div id="add_homescreen_widget">
  {form action=$active_homescreen_tab->getAddWidgetUrl($column_id)}
    {select_homescreen_widget_type user=$logged_user}
    
    {wrap_buttons}
      {submit}Add Home Screen Widget{/submit}
    {/wrap_buttons}
  {/form}
</div>