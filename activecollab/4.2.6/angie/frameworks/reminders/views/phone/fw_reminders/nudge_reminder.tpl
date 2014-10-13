{title}Nudge{/title}
{add_bread_crumb}Nudge{/add_bread_crumb}

<div id="reminder_nudge">
  {form action=$active_object->reminders()->getNudgeUrl()}
    {wrap field=send_to}
      {label for=reminderSendOn}Send Reminder To{/label}
      {select_reminder_to name="reminder[send_to]" value=$reminder_data.send_to select_users=$reminder_data.selected_users object=$active_object user=$logged_user id=reminderSendOn}
    {/wrap}

    {wrap field=selected_user_id}
		  {select_user name="reminder[selected_user_id]" object=$active_object->reminders()->getUsersContext() user=$logged_user id=reminderSelectedUserId}
		{/wrap}
    
    {wrap field=comment}
      {label for=reminderComment}Comment{/label}
      {editor_field name="reminder[comment]" id=reminderComment}{$reminder_data.comment nofilter}{/editor_field}
    {/wrap}
    
    {wrap_buttons}
      {submit}Nudge{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
	$(document).ready(function() {
		App.Wireframe.RadioButtons.init();
		App.Wireframe.SelectBox.init();
		
		$('#reminderSelectedUserId-button').parent().parent().hide();
	});
</script>