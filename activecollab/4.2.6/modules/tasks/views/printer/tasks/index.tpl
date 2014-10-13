{title}{$page_title}{/title}

{if is_foreachable($tasks)}

  {foreach from=$tasks key=map_name item=object_list name=print_object}
    <table class="tasks_table common" cellspacing="0">
      <thead>
      <tr>
        <th colspan="5">
          {$map_name}
        </th>
      </tr>
      </thead>
      <tbody>
      {foreach from=$object_list item=object}
        <tr>
          <td class="label">
            {if $object->label()->get() instanceof Label}
              {$object->label()->get()->getName()}
            {/if}
          </td>
          <td class="icon">
            {if $object->complete()->isCompleted()}
              {lang}Completed{/lang}
            {else}
              {lang}Active{/lang}
            {/if}
          </td>
          <td class="task_id" align="left">#{$object->getTaskId()}</td>
          <td class="name">{$object->getName()}</td>
          <td class="priority">{object_priority object=$object user=$logged_user show_normal=true}</td>
        </tr>
      {/foreach}
      </tbody>
    </table>
  {/foreach}
{else}
  <p>{lang}There are no Tasks that match this criteria{/lang}</p>
{/if}