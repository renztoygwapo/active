{title}Favorites{/title}
{add_bread_crumb}Favorites{/add_bread_crumb}

<div class="user_favorites">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Navigate{/lang}</li>
		{if is_foreachable($favorites)}
			{foreach $favorites as $favorite}
				<li><a href="{$favorite->getViewUrl()}"><span class="object_type {$favorite->getVerboseType(true)}">{$favorite->getVerboseType()}</span> {$favorite->getName()}</a></li>
			{/foreach}
		{else}
	  	<li>{lang}There are no favorites{/lang}</li>
		{/if}
	</ul>
</div>