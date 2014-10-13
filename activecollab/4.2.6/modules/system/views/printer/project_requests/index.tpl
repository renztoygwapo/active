<div id="print_container">
{title}{$page_title}{/title}
{if is_foreachable($project_requests)}
  {assign var=last_map_id value='--FIRST--'}
  {foreach from=$project_requests item=project_request name=print_project}
      {assign var=current_id value=call_user_func(array($project_request, $getter))}

      {if $current_id !== $last_map_id}
        {if !$smarty.foreach.print_project.first}
          </tbody>
          </table>
        {/if}
      
        
        <table class="tasks_table common" cellspacing="0">
          <thead>
            <tr>
              <th class="task_id" align="left">{lang}ID{/lang}</th>
              <th class="name">{lang}Project Request Name{/lang}</th>
            </tr>
          </thead>
          <tbody>
        {assign var=last_map_id value=$current_id}
      {/if}
    
		  <tr>
		    <td class="task_id" align="left">#{$project_request->getId()}</td>
		    <td class="name">{$project_request->getName()}</td>
		  </tr>    
  {/foreach}
  
	</tbody>
	</table>
  {else}
  <p class="empty_page">{lang}There are no Project Request that match this criteria{/lang}</p>
{/if}
</div>