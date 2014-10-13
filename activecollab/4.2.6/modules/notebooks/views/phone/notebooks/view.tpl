{title lang=false}{$active_notebook->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div id="notebook">
	{object object=$active_notebook user=$logged_user show_body=false}
    <div id="notebook_{$active_notebook->getId()}">
      <div class="notebook_cover">
				<img src="{$active_notebook->avatar()->getUrl(INotebookAvatarImplementation::SIZE_PHOTO)}" alt="" />
			</div>
      
      <div class="object_body_content">
				{if $active_notebook->getBody()}
				  {$active_notebook->getBody()|rich_text nofilter}
				{else}
				  {lang}No description for this Notebook{/lang}
				{/if}
      </div>
      
      {object_attachments object=$active_notebook user=$logged_user}
		</div>
  {/object}
	
	{if is_foreachable($notebook_pages)}
	  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
			<li data-role="list-divider"><img src="{image_url name="icons/listviews/navigate-pages.png" module=$smarty.const.NOTEBOOKS_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{lang}Pages{/lang}</li>
	  	{foreach $notebook_pages as $notebook_page}
	  		<li><a href="{$notebook_page->getViewUrl()}">{$notebook_page->getName()}<p class="ui-li-aside ui-li-desc">{lang version=$notebook_page->getVersion()}v:version{/lang}</p></a></li>
	  	{/foreach}
	  </ul>
  {/if}
  
  <div class="archived_objects">
		<a href="{assemble route=project_notebook_pages_archive project_slug=$active_project->getSlug() notebook_id=$active_notebook->getId()}" data-role="button" data-theme="k">{lang}Archive{/lang}</a>
	</div>
</div>