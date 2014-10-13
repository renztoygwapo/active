{title}{$page_title}{/title}

{if is_foreachable($invoices)}
	
	{foreach from=$invoices key=map_name item=object_list name=print_object}
		<table class="invoice_table common" cellspacing="0">
      <thead>
        <tr>
          <th colspan="{if $group_by == 'status' || $group_by == 'client_id'}3{else}4{/if}">{$map_name}</th>
        </tr>
      </thead>
      <tbody>
		  {foreach $object_list as $object}
		  <tr>
		    <td class="name">{$object->getName()}</td>
        {if $group_by != 'status'}
		    <td class="status" style="width: 20%">{$object->getVerboseStatus()}</td>
        {/if}
        {if $group_by != 'client_id'}
        <td class="client" style="width: 20%">
        {if $object->getCompany() instanceof Company}
          {$object->getCompany()->getName()}
        {/if}
        </td>
        {/if}
		    <td class="total right" style="width: 20%">{$object->getTotal()|money:$object->getCurrency():null:true:true}</td>
		  </tr>
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}There are no Invoices that match this criteria{/lang}</p>	
{/if}