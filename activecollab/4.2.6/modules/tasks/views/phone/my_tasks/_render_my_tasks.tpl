<div class="my_tasks">
  <ul data-role="listview" data-inset="true" data-dividertheme="j" data-theme="j">
    {if is_foreachable($assignments)}
      {foreach $assignments as $assignments_name}
        <li data-role="list-divider"><img src="{image_url name="icons/listviews/projects-icon.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" class="divider_icon" alt="">{$assignments_name.label}</li>
        {if is_foreachable($assignments_name.assignments)}
          {foreach $assignments_name.assignments as $assignment}
            {assignments_list_item object=$assignment urls=$urls project_slugs=$project_slugs}
          {/foreach}
        {/if}
      {/foreach}
    {else}
      <li>{lang}There are no assignments{/lang}</li>
    {/if}
  </ul>
</div>