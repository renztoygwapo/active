{title}New Reminder{/title}
{add_bread_crumb}New Reminder{/add_bread_crumb}

<div id="reminder_add">
  {form action=$active_object->reminders()->getAddUrl() method=post}
    <div class="reminder_date_picker">
	    {wrap field="send_on"}
	      {label for=reminderSendOn required=yes}Send Reminder On{/label}
	      {select_datetime name="reminder[send_on]" value=$reminder_data.send_on id=reminderSendOn class="required" minutes_step=15 default_hours=9}
	    {/wrap}
    </div>
    
    <script type="text/javascript">
      $('#reminder_add .reminder_date_picker img:first').attr('src', App.Wireframe.Utils.imageUrl('layout/reminder-date-picker-dropdown.png', 'reminders'));
    </script>
      
    {wrap_fields}
		  {wrap field="send_to" class="send_reminder_to"}
		    {label for=reminderSendOn}Send Reminder To{/label}
		    {select_reminder_to name="reminder[send_to]" value=$reminder_data.send_to select_users=$reminder_data.selected_users object=$active_object user=$logged_user}
		  {/wrap}
		    
		  {wrap field="comment" class="reminder_comment"}
		    {label for=reminderComment}Comment{/label}
        {textarea_field name="reminder[comment]" id=reminderComment}{$reminder_data.comment nofilter}{/textarea_field}
      {/wrap}
    {/wrap_fields}
  
    {wrap_buttons}
      {submit}Set Reminder{/submit}
    {/wrap_buttons}
  {/form}
</div>