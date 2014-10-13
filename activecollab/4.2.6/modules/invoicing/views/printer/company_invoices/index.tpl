{title}{$page_title}{/title}

{if is_foreachable($invoices)}
	
	{foreach from=$invoices key=map_name item=object_list name=print_object}
		<table class="invoice_table common" cellspacing="0">
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
		    <td class="invoice_id" align="left">#{$object->getId()}</td>
		    <td class="name">{$object->getName()}</td>
		  </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no Invoices that match this criteria{/lang}</p>	
{/if}