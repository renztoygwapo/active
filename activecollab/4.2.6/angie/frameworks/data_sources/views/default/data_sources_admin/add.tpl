<div id="add_data_source">
  {form action=Router::assemble('data_source_add')}
    {select_data_source user=$logged_user}

    {wrap_buttons}
      {submit}Add{/submit}
      {button test_url=DataSources::getTestConnectionUrl() id="test_connection_button"}Test Connection{/button}
    {/wrap_buttons}
  {/form}
</div>