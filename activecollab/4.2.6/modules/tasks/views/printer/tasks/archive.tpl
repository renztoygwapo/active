{title}{$page_title}{/title}

{if is_foreachable($tasks)}
  
  {assign var=last_map_id value='--FIRST--'}
  {foreach from=$tasks item=task name=print_tasks}
      {assign var=current_id value=call_user_func(array($task, $getter))}

      {if $current_id !== $last_map_id}
        {if !$smarty.foreach.print_tasks.first}
          </tbody>
          </table>
        {/if}
      
        <table class="tasks_table common" cellspacing="0">
          <thead>
            <tr>
              <th colspan="5">{if isset($map[$current_id])}{$map[$current_id]}{else}{lang}Unknown{/lang}{/if}</th>
            </tr>
          </thead>
          <tbody>
        {assign var=last_map_id value=$current_id}
      {/if}
    
		  <tr>
        <td class="label">
          {if $task->label()->get() instanceof Label}
            {$task->label()->get()->getName()}
          {/if}
        </td>
        <td class="icon">
          {if $task->complete()->isCompleted()}
            {image name="icons/12x12/checkbox-checked.png" module="complete"}
          {else}
            {image name="icons/12x12/checkbox-unchecked.png" module="complete"}
          {/if}
        </td>
		    <td class="task_id">#{$task->getTaskId()}</td>
		    <td class="name">{$task->getName()}</td>
		    <td class="priority">{object_priority object=$task user=$logged_user show_normal=true}</td>
		  </tr>    
  {/foreach}
  
	</tbody>
	</table>
  {else}
  <p class="empty_page">{lang}There are no Tasks that match this criteria{/lang}</p>
{/if}