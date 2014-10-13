{title}Update Job Type{/title}
{add_bread_crumb}Update Job Type{/add_bread_crumb}

<div id="update_job_type">
  {form action=$active_job_type->getEditUrl()}
  	{wrap_fields}
      {wrap field=name}
        {text_field name="job_type[name]" value=$job_type_data.name label="Name" required=true}
      {/wrap}
      
      {wrap field=update_default_hourly_rate}
        <label class="main_label">{lang}Change the Default Hourly Rate{/lang}</label>
          <div>
          	<input type="radio" name="job_type[update_default_hourly_rate]" id="dont_update_default_hourly_rate" {if !$job_type_data.update_default_hourly_rate}checked="checked"{/if} value="0"> <label for="dont_update_default_hourly_rate">{lang}No, Keep the Current Hourly Rate{/lang}</label>
          </div>
          <div>
        		<input type="radio" name="job_type[update_default_hourly_rate]" id="do_update_default_hourly_rate" {if $job_type_data.update_default_hourly_rate}checked="checked"{/if} value="1"> <label for="do_update_default_hourly_rate">{lang}Yes, Change the Default Hourly Rate{/lang}</label>
          </div>
        
        <div id="update_default_hourly_rate_settings" class="slide_down_settings">
          {wrap field=default_hourly_rate}
          	{money_field name="job_type[default_hourly_rate]" value=$job_type_data.default_hourly_rate label="Default Hourly Rate" required=true}
          {/wrap}
          
          {wrap field=update_default_hourly_rate_for}
            <div>
            	<input type="radio" name="job_type[update_default_hourly_rate_for]" id="update_default_hourly_rate_for_active_projects" {if $job_type_data.update_default_hourly_rate_for == 'active_projects'}checked="checked"{/if} value="active_projects"> <label for="update_default_hourly_rate_for_active_projects">{lang}Keep Old Hourly Rate on Completed Projects{/lang}</label>
            </div>
            
            <div>
            	<input type="radio" name="job_type[update_default_hourly_rate_for]" id="update_default_hourly_rate_for_all_projects" {if $job_type_data.update_default_hourly_rate_for == 'all_projects'}checked="checked"{/if} value="all_projects"> <label for="update_default_hourly_rate_for_all_projects">{lang}Update Default Hourly Rate for All Projects, Including Completed Project{/lang}</label>
            </div>

            <p class="aid" style="padding-left: 24px; margin-top: 6px"><b>{lang}Note{/lang}</b>: {lang}Project that have a custom hourly rate already set will not be updated. Controls above are designed to let you remember old hourly rate for completed projects, not to override existing custom rates{/lang}!</p>
          {/wrap}
        </div>
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#dont_update_default_hourly_rate:checked').each(function() {
    $(this).click(function() {
      $('#update_default_hourly_rate_settings').slideUp('fast');
    });
    
    if(this.checked) {
      $('#update_default_hourly_rate_settings').hide();
    } // if 
  });

  $('#do_update_default_hourly_rate').click(function() {
    var update_settings_wrapper = $('#update_default_hourly_rate_settings');
    
    update_settings_wrapper.slideDown('fast', function() {
      update_settings_wrapper.find('input[type=number]:first').focus();
    });
  });
</script>