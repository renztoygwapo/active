<tr class="item_form time_record_form edit_time_record" id="{$_project_time_form_id}">
  <td colspan="8" style="padding: 0 !important;">
    <form action="{$_project_time_form_row_record->getEditUrl()}" method="post" class="time_record_form">
      <div class="item_attributes">
        {if $can_track_for_others}
          <div class="item_attribute time_record_user user">
            {select_project_user name='time[user_id]' value=$time_record_data.user_id project=$active_project user=$logged_user optional=false id="{$_project_time_form_id}_user"}
          </div>
        {else}
          <input type="hidden" name="time[user_id]" value="{$time_record_data.user_id}"/>
        {/if}

        <div class="item_attribute item_value_wrapper time_record_job_type job_type">
          {select_job_type name='time[job_type_id]' id="{$_project_time_form_id}_job_type_id" value=$time_record_data.job_type_id user=$logged_user required=true}
        </div>

        <div class="item_attribute time_record_date date">
          {select_date name='time[record_date]' value=$time_record_data.record_date id="{$_project_time_form_id}_date"}
        </div>

        <div class="item_attribute item_value_wrapper time_record_value value">
          {text_field name='time[value]' value=$time_record_data.value id="{$_project_time_form_id}_value"}
        </div>

        <div class="item_attribute item_summary_wrapper item_summary time_record_summary summary">
          {text_field name='time[summary]' value=$time_record_data.summary id="{$_project_time_form_id}_summary"}
        </div>

        <div class="item_attribute time_record_billable billable">
          {select_billable_status name='time[billable_status]' value=$time_record_data.billable_status id="{$_project_time_form_id}_billable"}
        </div>
      </div>

      <div class="item_form_buttons">
        {submit}Log Time{/submit} {lang}or{/lang} <a href="#" class="item_form_cancel">{lang}Cancel{/lang}</a>
      </div>
    </form>
  </td>
</tr>