{title lang=false}{$active_task->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_task user=$logged_user}
	{object_attachments object=$active_task user=$logged_user}
	{object_subtasks object=$active_task user=$logged_user}
	
	<!--	Comments Block	-->
	{object_comments object=$active_task user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=task_comments}
	{render_comment_form object=$active_task id=task_comments}
{/object}