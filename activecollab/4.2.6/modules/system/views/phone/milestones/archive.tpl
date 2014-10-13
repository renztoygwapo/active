{title}Completed Milestones{/title}
{add_bread_crumb}Complete{/add_bread_crumb}

<div id="archived_milestones">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($milestones)}
	    {foreach $milestones as $milestone}
	    	<li><a href="{$milestone->getViewUrl()}">{$milestone->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}There are no completed milestones{/lang}</li>
		{/if}
	</ul>
</div>