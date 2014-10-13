<div id="object_estimate_set">
  {form action=$active_tracking_object->tracking()->getSetEstimateUrl()}
    {wrap_fields}
      {wrap field=estimate}
        {label required=true}Estimate{/label}
        {select_estimate name='estimate[value]' value=$estimate_data.value optional=false} {lang}of{/lang} {select_job_type name='estimate[job_type_id]' value=$estimate_data.job_type_id user=$logged_user optional=false}
      {/wrap}
      
      {wrap field=comment}
        {text_field name='estimate[comment]' label='Comment'}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Change Estimate{/submit}
    {/wrap_buttons}
  {/form}
</div>