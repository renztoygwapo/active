{title lang=false}{$active_time_record->getName()}{/title}
{add_bread_crumb}View Time Record{/add_bread_crumb}

{object object=$active_time_record user=$logged_user}
	<div class="object_tracking">
	  <div class="preview">{$active_time_record->getName()}</div>
	</div>
{/object}