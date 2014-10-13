{title}Archived Companies{/title}
{add_bread_crumb}Archived Companies{/add_bread_crumb}

<div id="archived_companies">
	<ul data-role="listview" data-inset="true" data-theme="f">
	{if is_foreachable($archived_companies)}
    {foreach $archived_companies as $company}
    	<li class="company_row"><a href="{$company->getViewUrl()}"><img src="{image_url name="layout/avatars/CompanyIcon64x64.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt=""/>{$company->getName()}</a></li>
	  	{assign var=users value=$company->getUsers()}
	  	{if is_foreachable($users)}
		  	{foreach $users as $user}
					<li class="user_row"><a href="{$user->getViewUrl()}"><img class="ui-li-icon" src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt=""/>{$user->getName()}</a></li>
		  	{/foreach}
	  	{/if}
    {/foreach}
	{else}
		<li data-theme="j">{lang}There are no companies in the archive{/lang}</li>
	{/if}
	</ul>
</div>