{title}Files{/title}
{add_bread_crumb}Active Files{/add_bread_crumb}

<div id="assets">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Files{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/files.png' module=$smarty.const.FILES_MODULE}" alt=""/></div>
      <div class="objects_list_details_actions">
        <ul>
          {if $can_add_assets}
            <li><a href="{$project_assets_files_add}" id="assets_new_upload_files">{lang}Upload Files{/lang}</a></li>
            <li><a href="{$project_assets_text_document_add}" id="assets_new_text_document">{lang}New Text Document{/lang}</a></li>
          {/if}
          {if $manage_categories_url}<li><a href="{$manage_categories_url}" class="manage_objects_list_categories" title="{lang}Manage Categories{/lang}">{lang}Manage File Categories{/lang}</a></li>{/if}
        </ul>
      </div>
      {if $can_manage_assets}
        <div class="object_list_details_additional_actions">
          <a href="{assemble route='project_assets_archive' project_slug=$active_project->getSlug()}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
        </div>
      {/if}

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
	        <li>{lang}To select a file and load its details, please click on it in the list on the left{/lang}</li>
	        <li>{lang}It is possible to select multiple files at the same time. Just hold Ctrl key on your keyboard and click on all the files that you want to select{/lang}</li>
        </ul>
      </div>  
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'assets', $smarty.const.FILES_MODULE)}