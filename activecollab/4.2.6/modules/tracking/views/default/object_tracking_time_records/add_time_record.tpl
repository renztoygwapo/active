{title}Log Time{/title}
{add_bread_crumb}Log Time{/add_bread_crumb}

<div id="add_time_record">
  {form action=$active_tracking_object->tracking()->getAddTimeUrl() method=post}
    <div class="fields_wrapper">
	    {wrap field=value}
	    	{text_field name='time_record[value]' value=$time_record_data.value class=short label='Hours' required=yes} {lang}of{/lang} {select_job_type name='time_record[job_type_id]' value=$time_record_data.job_type_id user=$logged_user required=true}
	    	<span class="details block">{lang}Possible formats: 3:30 or 3.5{/lang}</span>
	    {/wrap}
	  
	    {wrap field=summary}
	      {text_field name='time_record[summary]' value=$time_record_data.summary label="Summary"}
	    {/wrap}
	  
	    {wrap field=record_date}
	    	{select_date name='time_record[record_date]' value=$time_record_data.record_date label="Date" required=true}
	    {/wrap}

      {if $can_track_for_others}
        {wrap field=user_id}
          {select_project_user name='time_record[user_id]' value=$time_record_data.user_id label="User" project=$active_project user=$logged_user optional=false required=true}
        {/wrap}
      {else}
        <input type="hidden" name="time_record[user_id]" value="{$time_record_data.user_id}" />
      {/if}
	    
	    {wrap field=billable_status}
	      {select_billable_status name='time_record[billable_status]' value=$time_record_data.billable_status label='Is Billable?'}
	    {/wrap}
    </div>
  
    {wrap_buttons}
    	{submit}Log Time{/submit}
    {/wrap_buttons}
  {/form}
</div>