<div id="edit_data_source">
  {form action=$active_data_source->getEditUrl()}
    {select_data_source user=$logged_user active_data_source=$active_data_source}
    
    {wrap_buttons}
      {submit}Change{/submit}
      {button test_url=DataSources::getTestConnectionUrl() id="test_connection_button"}Test Connection{/button}
    {/wrap_buttons}
  {/form}
</div>