{title}{$page_title}{/title}

{if is_foreachable($quotes)}
  
  {assign var=last_map_id value='--FIRST--'}
  {foreach from=$quotes item=quote name=print_tasks}
      {assign var=current_id value=call_user_func(array($quote, $getter))}

      {if $current_id !== $last_map_id}
        {if !$smarty.foreach.print_tasks.first}
          </tbody>
          </table>
        {/if}
      
        <h2>{if isset($map[$current_id])}{$map[$current_id]}{else}{lang}Unknown{/lang}{/if}</h2>
        <table class="quote_table common" cellspacing="0">
          <thead>
            <tr>
              <th class="name">{lang}Quote Name{/lang}</th>
            </tr>
          </thead>
          <tbody>
        {assign var=last_map_id value=$current_id}
      {/if}
    
		  <tr>
		    <td class="quote_name">{$quote->getName(true)}</td>
		  </tr>    
  {/foreach}
  
	</tbody>
	</table>
  {else}
  <p class="empty_page">{lang}There are no Quotes that match this criteria{/lang}</p>
{/if}