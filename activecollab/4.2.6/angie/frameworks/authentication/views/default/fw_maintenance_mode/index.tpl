<div id="maintenance_mode_settings">
  {form action=Router::assemble('maintenance_mode_settings')}
    {wrap_fields}
      {wrap field=project_id}
        {label for=maintenanceEnabled required=yes}Enable Maintenance Mode{/label}
        {yes_no name='maintenance[maintenance_enabled]' value=$maintenance_data.maintenance_enabled id=maintenanceEnabled}
      {/wrap}
      
      {wrap field=project_id}
        {label for=maintenanceMessage}Maintenance Message{/label}
        {textarea_field name='maintenance[maintenance_message]' id=maintenanceMessage}{$maintenance_data.maintenance_message nofilter}{/textarea_field}
      {/wrap}

      <p class="aid">{lang}Please note that when Maintenance Mode is turned on only users with administrator role can log in activeCollab{/lang}</p>
    {/wrap_fields}


    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}

</div>