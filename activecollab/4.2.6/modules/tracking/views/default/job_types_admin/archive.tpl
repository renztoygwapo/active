{title}Archive Job Type{/title}
{add_bread_crumb}Archive Job Type{/add_bread_crumb}

<div id="archive_job_type">
  {form action=$active_job_type->getArchiveUrl()}
  	{wrap_fields}
      {wrap field=replaceJobType}
        <p class="archive_job_type_message">{lang users_count=$job_type_data.used_by_users_count}This Job Type is used by :users_count user(s).{/lang}</p>
          <div>
          	<input type="radio" name="job_type[replace_job_type]" id="dont_replace_job_type" {if !$job_type_data.replace_job_type}checked="checked"{/if} value="0"> <label for="dont_replace_job_type">{lang}Make All Job Types Available to These Users{/lang}</label>
          </div>
          <div>
        		<input type="radio" name="job_type[replace_job_type]" id="do_replace_job_type" {if $job_type_data.replace_job_type}checked="checked"{/if} value="1"> <label for="do_replace_job_type">{lang}Replace With This Job Type for These Users{/lang}</label>
          </div>
        
        <div id="replace_job_type_settings" class="slide_down_settings">
          {wrap field=jobTypeId}
            {select_job_type name='job_type[job_type_id]' value=$job_type_data.job_type_id exclude_ids=$active_job_type->getId() user=$logged_user label="Select Job Type" required=true}
          {/wrap}
        </div>
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Archive Job Type{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#dont_replace_job_type:checked').each(function() {
    $(this).click(function() {
      $('#replace_job_type_settings').slideUp('fast');
    });
    
    if(this.checked) {
      $('#replace_job_type_settings').hide();
    } // if 
  });

  $('#do_replace_job_type').click(function() {
    var replace_settings_wrapper = $('#replace_job_type_settings');

    replace_settings_wrapper.slideDown('fast', function() {
      replace_settings_wrapper.find('select:first').focus();
    });
  });
</script>