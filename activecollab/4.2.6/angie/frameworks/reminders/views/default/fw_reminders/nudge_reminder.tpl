{title}Nudge{/title}
{add_bread_crumb}Nudge{/add_bread_crumb}

<div id="reminder_nudge">
  {form action=$active_object->reminders()->getNudgeUrl() method=post}
    <div class="fields_wrapper">
	    {wrap field="send_to"}
	      {label for=reminderSendOn}Send Reminder To{/label}
	      {select_reminder_to name="reminder[send_to]" value=$reminder_data.send_to select_users=$reminder_data.selected_users object=$active_object user=$logged_user id=reminderSendOn}
	    {/wrap}
	    
	    {wrap field="comment"}
	      {label for=reminderComment}Comment{/label}
	      {textarea_field name="reminder[comment]" id=reminderComment}{$reminder_data.comment nofilter}{/textarea_field}
	    {/wrap}
    </div>
  
    {wrap_buttons}
      {submit}Nudge{/submit}
    {/wrap_buttons}
  {/form}
</div>