{title}Add Repository{/title}
{add_bread_crumb}Add Repository{/add_bread_crumb}

<div id="repository_choose" class="flyout_tab_content">
  {if is_foreachable($existing_repositories)}
		{form action=$repository_add_url method=post ask_on_leave=yes autofocus=yes}
		    
		 <div class="fields_wrapper">
				{wrap field=chooseRepository}
					{label for=repositoryExisting}{lang}Choose a repository:{/lang}{/label}
					{select_repository_choose_existing name='repository[source_repository_id]' id=repositoryExisting data=$existing_repositories}
				{/wrap}
				        
				{if $logged_user->canSeePrivate()}
					{wrap field=visibility}
						{label for=repositoryVisibility}Visibility{/label}
						{select_visibility name='repository[visibility]' value=$repository_data.visibility object=$project_object_repository}
					{/wrap}
				{else}
		  		<input type="hidden" name="repository[visibility]" value="1"/>
				{/if}
			</div>    
			      
			{wrap_buttons}
		  	{submit}Add Repository{/submit}
			{/wrap_buttons}
		{/form}
  {else}
    <p class="empty_page">{lang}There are no repositories to add{/lang}</p>
  {/if}  
</div>