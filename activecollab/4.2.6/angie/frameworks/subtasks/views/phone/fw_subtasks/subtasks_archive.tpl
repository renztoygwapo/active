{title}Completed Subtasks{/title}
{add_bread_crumb}Complete{/add_bread_crumb}

<div id="archived_subtasks">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($subtasks)}
	    {foreach $subtasks as $subtask}
	    	<li><a href="{$subtask->getViewUrl()}">{$subtask->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}No completed subtasks{/lang}</li>
		{/if}
	</ul>
</div>