{title lang=false}{$active_milestone->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_milestone user=$logged_user}
	<div class="wireframe_content_wrapper">
	  {inline_tabs object=$active_milestone}
	</div>
	
	<div class="wireframe_content_wrapper">
		{object_comments object=$active_milestone user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=milestone_comments}
		{render_comment_form object=$active_milestone id=milestone_comments}
	</div>
{/object}