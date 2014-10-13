{title}All Recurring Profiles{/title}
{add_bread_crumb}All Recurring Profiles{/add_bread_crumb}

<div id="recurring_profiles">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  	{if is_foreachable($formatted_recurring_profiles)}
	  	{foreach $formatted_recurring_profiles as $company_name => $recurring_profiles}
		  	{if is_foreachable($recurring_profiles)}
		  		<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-companies-icon.png" module=$smarty.const.INVOICING_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{$company_name}</li>
			  	{foreach $recurring_profiles as $recurring_profile}
	  				<li><a href="{$recurring_profile.permalink}">{$recurring_profile.name}</a></li>
			  	{/foreach}
		  	{/if}
	  	{/foreach}
	  {else}
	  	<li>{lang}There are no Recurring Profiles{/lang}</li>
	  {/if}
  </ul>
</div>