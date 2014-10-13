{title}Log Time{/title}
{add_bread_crumb}Log Time{/add_bread_crumb}

<div id="add_time_record">
  {form action=$active_tracking_object->tracking()->getAddTimeUrl()}
    {wrap field=value}
    	{text_field name='time_record[value]' value=$time_record_data.value id=timeRecordValue label='Hours' required=true}
    	<span class="details block">{lang}Possible formats: 3:30 or 3.5{/lang}</span>
    {/wrap}
    
    {wrap field=job_type}
    	{select_job_type name='time_record[job_type_id]' value=$time_record_data.job_type_id user=$logged_user id=timeRecordJobType label='Job Type' required=true}
    {/wrap}
    
    {wrap field=summary}
      {text_field name='time_record[summary]' value=$time_record_data.summary label='Summary' id=timeRecordSummary}
    {/wrap}
    
    {wrap field=record_date}
    	{select_date name='time_record[record_date]' value=$time_record_data.record_date id=timeRecordRecordDate label='Date' required=true}
    {/wrap}
    
    {wrap field=user_id}
    	{select_project_user name='time_record[user_id]' value=$time_record_data.user_id label='User' project=$active_project user=$logged_user optional=false id=timeRecordUserId required=true}
    {/wrap}
    
    {wrap field=billable_status}
      {select_billable_status name='time_record[billable_status]' value=$time_record_data.billable_status label='Is Billable?' id=timeIsBillable}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Log Time{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>