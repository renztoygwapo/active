{title}New Job Type{/title}
{add_bread_crumb}New Job Type{/add_bread_crumb}

<div id="new_job_type">
  {form action=Router::assemble('job_types_add')}
    {wrap_fields}
      {wrap field=name}
        {text_field name="job_type[name]" value=$job_type_data.name label="Name" required=true}
      {/wrap}
      
      {wrap field=default_hourly_rate}
        {money_field name="job_type[default_hourly_rate]" value=$job_type_data.default_hourly_rate label="Default Hourly Rate" required=true}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Add Job Type{/submit}
    {/wrap_buttons}
  {/form}
</div>