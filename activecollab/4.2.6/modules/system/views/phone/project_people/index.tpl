{title}People{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="project_people">
	<ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
  	{if is_foreachable($formatted_project_users)}
	  	{foreach $formatted_project_users as $company_id => $project_users}
		  	{if is_foreachable($project_users)}
		  		{assign var=company_name value=$companies[$company_id]}
		  		
		  		<li data-role="list-divider">{$company_name}</li>
		  		{if is_foreachable($project_users)}
				  	{foreach $project_users as $user}
				  		{assign_var name=role}
						    {if $user.user.id == $active_project->getLeaderId()}
						    	{lang}Leader{/lang}
						    {elseif $user.user.is_administrator == true}
						      {lang}Full Access (Administrator){/lang}
						    {elseif $user.user.is_project_manager == true}
						      {lang}Full Access (Project Manager){/lang}
						    {else}
						      {$user.role}
						    {/if}
						  {/assign_var}
						  
				  		<li>
				  			<img class="ui-li-icon" src="{$user.user.avatar.large}" alt=""/>
			  				<h3 class="ui-li-heading">{$user.user.name}</h3>
			  				<p class="ui-li-desc">{lang}Email{/lang}: {$user.user.email}</p>
			  				<p class="ui-li-aside">{$role}</p>
				  		</li>
				  	{/foreach}
			  	{/if}
		  	{/if}
	  	{/foreach}
	  {/if}
  </ul>
</div>