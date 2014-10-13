{title}Tasks{/title}
{add_bread_crumb}Tasks{/add_bread_crumb}

<div id="milestone_tasks">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.TASKS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Tasks{/lang}</li>
		{if is_foreachable($tasks)}
			{foreach $tasks as $task}
				<li><a href="{replace search='--TASKID--' in=$task_url replacement=$task.task_id}">{$task.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active tasks{/lang}</li>
		{/if}
	</ul>
</div>