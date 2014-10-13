{title}Discussions{/title}
{add_bread_crumb}Discussions{/add_bread_crumb}

<div id="milestone_discussions">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate.png" module=$smarty.const.DISCUSSIONS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Discussions{/lang}</li>
		{if is_foreachable($discussions)}
			{foreach $discussions as $discussion}
				<li><a href="{replace search='--DISCUSSIONID--' in=$discussion_url replacement=$discussion.id}">{$discussion.name}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no active discussions{/lang}</li>
		{/if}
	</ul>
</div>