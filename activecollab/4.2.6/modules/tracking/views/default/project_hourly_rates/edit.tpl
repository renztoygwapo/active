<div id="update_project_hourly_rate">
  {form action=$active_job_type->getProjectHourlyRateUrl($active_project)}
    <div>
      <input type="radio" name="project_hourly_rate[use_custom]" id="dont_use_custom_project_hourly_rate" {if !$project_hourly_rate_data.use_custom}checked="checked"{/if} value="0"> <label for="dont_use_custom_project_hourly_rate">{lang}Use Default Hourly Rate{/lang} ({$active_job_type->getDefaultHourlyRate()|money})</label>
    </div>
    
    <div>
      <input type="radio" name="project_hourly_rate[use_custom]" id="use_custom_project_hourly_rate" {if $project_hourly_rate_data.use_custom}checked="checked"{/if} value="1"> <label for="use_custom_project_hourly_rate">{lang}Specify a Different Hourly Rate for this Project{/lang}</label>
    </div>
    
    <div id="custom_project_hourly_rate_settings" class="slide_down_settings">
      {wrap field=hourly_rate}
        {money_field name="project_hourly_rate[hourly_rate]" value=$project_hourly_rate_data.hourly_rate label="Hourly Rate" required=true}
      {/wrap}
    </div>
  
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#dont_use_custom_project_hourly_rate').each(function() {
    if(this.checked) {
      $('#update_project_hourly_rate #custom_project_hourly_rate_settings').hide();
    } else {
      $('#update_project_hourly_rate #custom_project_hourly_rate_settings input[type=number]:first').focus();
    } // if

    $(this).click(function() {
      $('#update_project_hourly_rate #custom_project_hourly_rate_settings').slideUp('fast');
    });
  });

  $('#use_custom_project_hourly_rate').click(function() {
    $('#update_project_hourly_rate #custom_project_hourly_rate_settings').slideDown('fast', function() {
      $(this).find('input[type=number]:first').focus();
    });
  });
  
  $('#update_project_hourly_rate form div:eq(0), #update_project_hourly_rate form div:eq(1)').css('padding', '0 20px');
</script>