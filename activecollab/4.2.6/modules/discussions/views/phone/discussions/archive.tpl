{title}Archived Discussions{/title}
{add_bread_crumb}Archive{/add_bread_crumb}

<div id="archived_discussions">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($discussions)}
	    {foreach $discussions as $discussion}
	    	<li><a href="{$discussion->getViewUrl()}">{$discussion->getName()}</a></li>
	    {/foreach}
		{else}
			<li>{lang}No archived Discussions{/lang}</li>
		{/if}
	</ul>
</div>