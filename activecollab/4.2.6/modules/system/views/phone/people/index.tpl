{title}People{/title}
{add_bread_crumb}Companies{/add_bread_crumb}

<div id="people">
	{if is_foreachable($companies)}
	  <ul data-role="listview" data-inset="true" data-theme="f">
		  {foreach $companies as $company}
		  	<li class="company_row"><a href="{$company->getViewUrl()}"><img src="{image_url name="layout/avatars/CompanyIcon64x64.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt=""/>{$company->getName()}</a></li>
		  	{assign var=users value=$company->getUsers($visible_user_ids)}
		  	{if is_foreachable($users)}
			  	{foreach $users as $user}
			  		<li class="user_row"><a href="{$user->getViewUrl()}"><img class="ui-li-icon" src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt=""/>{$user->getName()}</a></li>
			  	{/foreach}
		  	{/if}
		  {/foreach}
	  </ul>
	{/if}
	
	<div class="archived_objects">
  	<a href="{$archived_companies_url}" data-role="button" data-theme="k">Archived Companies</a>
  </div>
</div>