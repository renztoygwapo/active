{title}All Tasks{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="tasks">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($tasks)}
			{foreach $tasks as $task}
				<li><a href="{replace search='--TASKID--' in=$task_url replacement=$task.task_id}">#{$task.task_id} {$task.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active tasks in this project{/lang}</li>
		{/if}
	</ul>
</div>

<div class="archived_objects">
	<a href="{assemble route=project_tasks_archive project_slug=$active_project->getSlug()}" data-role="button" data-theme="k">{lang}Completed Tasks{/lang}</a>
</div>