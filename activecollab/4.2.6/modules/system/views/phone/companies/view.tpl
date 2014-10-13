{title lang=false}{$active_company->getName()}{/title}
{add_bread_crumb lang=false}Details{/add_bread_crumb}

{object object=$active_company user=$logged_user}
	<div class="company_users">
	  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	    <li data-role="list-divider"><img src="{image_url name="icons/listviews/users-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Users{/lang}</li>
	  	{if is_foreachable($users)}
		  	{foreach $users as $user}
		  		<li><a href="{$user->getViewUrl()}"><img class="ui-li-icon" src="{$user->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)}" alt=""/>{$user->getName()}</a></li>
		  	{/foreach}
		  {else}
		  	<li>{lang}This company has no users{/lang}</li>
		  {/if}
	  </ul>
	</div>
  
  <div class="company_projects">
		<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
	    <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Projects{/lang}</li>
	  	{if is_foreachable($active_projects)}
		  	{foreach $active_projects as $active_project}
		  		<li><a href="{$active_project->getViewUrl()}"><img class="ui-li-icon" src="{$active_project->avatar()->getUrl(IProjectAvatarImplementation::SIZE_BIG)}" alt=""/>{$active_project->getName()}</a></li>
		  	{/foreach}
		  {else}
		  	<li>{lang}This company has no active projects{/lang}</li>
	  	{/if}
	  </ul>
	</div>

  {if $can_access_company_invoices}
  <div class="company_invoices">
  	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  		<li data-role="list-divider"><img src="{image_url name="icons/listviews/invoice-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Invoices{/lang}</li>
	  	{if is_foreachable($formatted_invoices)}
		  	{foreach $formatted_invoices as $status => $invoices}
			  	{if is_foreachable($invoices)}
			  		{assign_var name=list_divider}
					    {if $status == $smarty.const.INVOICE_STATUS_DRAFT}
					    	{lang}Draft{/lang}
					    {elseif $status == $smarty.const.INVOICE_STATUS_ISSUED}
					      {lang}Issued{/lang}
					    {elseif $status == $smarty.const.INVOICE_STATUS_PAID}
					      {lang}Paid{/lang}
					    {elseif $status == $smarty.const.INVOICE_STATUS_CANCELED}
					      {lang}Canceled{/lang}
					    {/if}
					  {/assign_var}
					  
			  		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-{$list_divider|lower|trim}-icon.png" module=$smarty.const.INVOICING_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{$list_divider}</li>
				  	{foreach $invoices as $invoice}
		  				<li><a href="{$invoice.permalink}"><img class="ui-li-icon" src="{image_url name="layout/avatars/invoice-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt=""/>{$invoice.name}</a></li>
				  	{/foreach}
			  	{/if}
		  	{/foreach}
		  {else}
		  	<li>{lang}This company has no Invoices{/lang}</li>
		  {/if}
	  </ul>
  </div>
  {/if}
{/object}

<div class="archived_objects">
	<a href="{$completed_projects_url}" data-role="button" data-theme="k">Completed Projects</a>
</div>