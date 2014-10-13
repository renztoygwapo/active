{title}Users{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="company_users">
	<ul data-role="listview">
  	{if is_foreachable($users)}
	  	{foreach $users as $user}
	  		<li><a href="{$user->getViewUrl()}"><img class="ui-li-icon" src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)}" alt=""/>{$user->getName()}<p class="ui-li-aside ui-li-desc">{if $user->getConfigValue('title')}{$user->getConfigValue('title')}{/if}</p></a></li>
	  	{/foreach}
  	{else}
			<li>{lang}There are no users in this company{/lang}</li>
	  {/if}
  </ul>
</div>