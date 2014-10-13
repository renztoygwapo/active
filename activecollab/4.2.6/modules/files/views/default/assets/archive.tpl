{title}File Archive{/title}
{add_bread_crumb}Archived Files{/add_bread_crumb}

<div id="assets">
  <div class="empty_content">
    <div class="objects_list_title">{lang}File Archive{/lang}</div>
    <div class="objects_list_icon"><img src="{image_url name='icons/48x48/files.png' module=$smarty.const.FILES_MODULE}" alt=""/></div>
    <div class="object_list_details_additional_actions">
      <a href="{assemble route='project_assets' project_slug=$active_project->getSlug()}" id="view_archive"><span>{lang}Browse Active{/lang}</span></a>
    </div>
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