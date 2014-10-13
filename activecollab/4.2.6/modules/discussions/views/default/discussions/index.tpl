{title}Discussions{/title}
{add_bread_crumb}List{/add_bread_crumb}

<div id="discussions">
  <div class="empty_content">
      <div class="objects_list_title">{lang}Discussions{/lang}</div>
      <div class="objects_list_icon"><img src="{image_url name='icons/48x48/discussions.png' module=discussions}" alt=""/></div>
      <div class="objects_list_details_actions">
        <ul>
          {if $add_discussion_url}<li><a href="{assemble route='project_discussions_add' project_slug=$active_project->getSlug()}" id="new_discussion">{lang}New Discussion{/lang}</a></li>{/if}
          {if $manage_categories_url}<li><a href="{$manage_categories_url}" class="manage_objects_list_categories" title="{lang}Manage Discussion Categories{/lang}">{lang}Manage Categories{/lang}</a></li>{/if}
        </ul>
      </div>

      {if $can_manage_discussions}
        <div class="object_list_details_additional_actions">
          <a href="{assemble route='project_discussions_archive' project_slug=$active_project->getSlug()}" id="view_archive"><span><img src="{image_url name="icons/12x12/archive.png" module="environment"}">{lang}Browse Archive{/lang}</span></a>
        </div>
      {/if}

      <div class="object_lists_details_tips">
        <h3>{lang}Tips{/lang}:</h3>
        <ul>
          <li>{lang}To select a discussion and load its details, please click on it in the list on the left{/lang}</li>
          <li>{lang}It is possible to select multiple discussions at the same time. Just hold Ctrl key on your keyboard and click on all the discussions that you want to select{/lang}</li>
        </ul>
      </div>
  </div>
</div>

{include file=get_view_path('_initialize_objects_list', 'discussions', $smarty.const.DISCUSSIONS_MODULE)}