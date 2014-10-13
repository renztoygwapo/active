{title}{$page_title}{/title}

{if is_foreachable($assets)}
	
	{foreach from=$assets key=map_name item=object_list name=print_object}
		<table class="assets_table common" cellspacing="0">
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
		  	<td class="icon" align="left">
  		  	  <img src="{$object->preview()->getSmallIconUrl()}"/>
            </td>
		    <td class="asset_id" align="left">#{$object->getId()}</td>
		    <td class="name">{$object->getName()}</td>
		  </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no Assets that match this criteria{/lang}</p>	
{/if}
