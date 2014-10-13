{title}Edit Time Record{/title}
{add_bread_crumb}Edit Time Record{/add_bread_crumb}

<div id="edit_time_record" data-rel="dialog">
  {form action=$active_time_record->getEditUrl()}
  	{wrap field=user_id}
    	{select_project_user name='time_record[user_id]' value=$time_record_data.user_id label='User' project=$active_project user=$logged_user optional=false id=timeUserId required=true}
    {/wrap}
    
    {wrap field=value}
    	{text_field name='time_record[value]' value=$time_record_data.value id=timeValue label='Hours' required=true}
    	<span class="details block">{lang}Value can be inserted in decimal (5.25) and HH:MM (5:15) format{/lang}</span>
    {/wrap}
    
    {wrap field=job_type}
    	{select_job_type name='time_record[job_type_id]' value=$time_record_data.job_type_id user=$logged_user id=timeRecordJobType label='Job Type' required=true}
    {/wrap}
    
    {wrap field=record_date}
    	{select_date name='time_record[record_date]' value=$time_record_data.record_date id=timeRecordDate label='Date' required=true}
    {/wrap}
    
    {wrap field=summary}
      {text_field name='time_record[summary]' value=$time_record_data.summary label='Summary' id=timeSummary}
    {/wrap}
    
    {wrap field=billable_status}
      {select_billable_status name='time_record[billable_status]' value=$time_record_data.billable_status label='Is Billable?' id=timeIsBillable}
    {/wrap}
    
    {wrap_buttons}
    	{submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.SelectBox.init();
		App.Wireframe.DateBox.init();
	});
</script>