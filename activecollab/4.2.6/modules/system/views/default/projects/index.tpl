{title}Projects{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div id="projects">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Projects{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/projects.png' module=$smarty.const.SYSTEM_MODULE}" alt=""/></div>

      <div class="objects_list_details_actions">
        {if $can_add_project || $manage_categories_url}
          <ul>
            {if $can_add_project}<li><a href="{assemble route='projects_add'}" id="new_project">{lang}New Project{/lang}</a></li>{/if}
            {if $manage_categories_url}<li><a href="{$manage_categories_url}" class="manage_objects_list_categories" title="{lang}Manage Project Categories{/lang}">{lang}Manage Categories{/lang}</a></li>{/if}
          </ul>
        {/if}
      </div>

      {if $can_add_project}
        <div class="object_list_details_additional_actions">
          <a href="{assemble route='projects_archive'}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
        </div>
      {/if}

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a project and load its details, please click on it in the list on the left{/lang}</li>
          <li>{lang}It is possible to select multiple projects at the same time. Just hold Ctrl key on your keyboard and click on all the projects that you want to select{/lang}</li>
        </ul>
      </div>
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'projects', $smarty.const.SYSTEM_MODULE)}