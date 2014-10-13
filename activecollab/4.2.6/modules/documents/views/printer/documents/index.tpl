{title}{$page_title}{/title}

{if is_foreachable($documents)}
  
  {assign var=last_map_id value='--FIRST--'}
  {foreach from=$documents item=document name=print_tasks}
      {assign var=current_id value=call_user_func(array($document, $getter))}

      {if $current_id !== $last_map_id}
        {if !$smarty.foreach.print_tasks.first}
          </tbody>
          </table>
        {/if}
      
        <h2>{if isset($map[$current_id])}{$map[$current_id]}{else}{lang}Unknown{/lang}{/if}</h2>
        <table class="common" cellspacing="0">
          <thead>
            <tr>
              <th class="docs_id">{lang}ID{/lang}</th>
              <th class="name" align="left">{lang}Document Name{/lang}</th>
           </tr>
          </thead>
          <tbody>
        {assign var=last_map_id value=$current_id}
      {/if}
    
		  <tr>
		    <td class="item_id">#{$document->getId()}</td>
		    <td class="item_name">{$document->getName()}</td>
		  </tr>    
  {/foreach}
  
	</tbody>
	</table>
  {else}
  <p class="empty_page">{lang}There are no Documents that match this criteria{/lang}</p>
{/if}