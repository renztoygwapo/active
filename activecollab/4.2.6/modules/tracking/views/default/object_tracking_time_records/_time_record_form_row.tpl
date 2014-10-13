{if $_project_time_form_row_record instanceof TimeRecord && $_project_time_form_row_record->isLoaded()}
<tr class="item_form time_record_form edit_time_record" id="{$_project_time_form_id}">
{else}
<tr class="item_form time_record_form new_time_record" id="{$_project_time_form_id}">
{/if}
  <td colspan="5">
    {if $_project_time_form_row_record instanceof TimeRecord && $_project_time_form_row_record->isLoaded()}
    <form action="{$_project_time_form_row_record->getEditUrl()}" method="post" class="time_record_form">
    {else}
    <form action="{$active_project->tracking()->getAddTimeUrl()}" method="post" class="time_record_form">
    {/if}
    
      <div class="item_attributes">

        {if $can_track_for_others}
          <div class="item_attribute time_record_user">
            {label for="($_project_time_form_id)_user" required=yes}User{/label} {select_project_user name='time[user_id]' value=$time_record_data.user_id project=$active_project user=$logged_user optional=false id="{$_project_time_form_id}_user"}
          </div>
        {else}
          <input type="hidden" name="time[user_id]" value="{$time_record_data.user_id}"/>
        {/if}
      
        <div class="item_attribute item_value_wrapper time_record_value">
          {label for="($_project_time_form_id)_value" required=yes}Hours{/label} {text_field name='time[value]' value=$time_record_data.value id="{$_project_time_form_id}_value"} {lang}of{/lang} {select_job_type name='time[job_type_id]' id="{$_project_time_form_id}_job_type_id" value=$time_record_data.job_type_id user=$logged_user required=true}
        </div>
        
        <div class="item_attribute time_record_date">
          {label for="($_project_time_form_id)_date" required=yes}Date{/label} {select_date name='time[record_date]' value=$time_record_data.record_date id="{$_project_time_form_id}_date"}
        </div>
        
        <div class="item_attribute item_summary_wrapper item_summary time_record_summary">
          {label for="($_project_time_form_id)_summary"}Summary{/label} {text_field name='time[summary]' value=$time_record_data.summary id="{$_project_time_form_id}_summary"}
        </div>
        
        <div class="item_attribute time_record_billable">
          {label for="($_project_time_form_id)_billable"}Billable?{/label} {select_billable_status name='time[billable_status]' value=$time_record_data.billable_status id="{$_project_time_form_id}_billable"}
        </div>
      </div>
      
      <p class="details">{lang}Value can be inserted in decimal (5.25) and HH:MM (5:15) format{/lang}</p>
      
      <div class="item_form_buttons">
        {submit}Log Time{/submit} {lang}or{/lang} <a href="#" class="item_form_cancel">{lang}Cancel{/lang}</a>
      </div>
    </form>
  </td>
</tr>