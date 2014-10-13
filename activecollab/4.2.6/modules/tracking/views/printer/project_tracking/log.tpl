{title lang=lang}{$active_project->getName()}: {lang}Time and Expenses{/lang}{/title}

{if is_foreachable($items)}
	{foreach $items as $date => $records}
		<table class="tiemexpenses_table common" cellspacing="0">
      <thead>
        <tr>
          <th colspan="5">{$date}</th>
        </tr>
      </thead>
      <tbody>
		  {foreach $records as $record}
		  <tr>
		  	<td class="value" align="left">
        {if $record['type'] == 'TimeRecord'}
          {$record['value']|hours}h
        {else}
          {$record['value']|money:$project_currency}
        {/if}
		  	</td>
        <td class="user">{$record.user_display_name}</td>
        <td class="summary details" align="left">
        {if ($record.parent_type == 'Task' && $record.parent_name) && $record.summary}
          {lang name=$record.parent_name}Task: :name{/lang} ({$record.summary})
        {elseif $record.parent_type == 'Task' && $record.parent_name}
          {lang name=$record.parent_name}Task: :name{/lang}
        {elseif $record.summary}
          {$record.summary}
        {/if}
        </td>
        <td class="status details" align="left">
        {if $record.billable_status == $smarty.const.BILLABLE_STATUS_NOT_BILLABLE}
          {lang}Not Billable{/lang}
        {elseif $record.billable_status == $smarty.const.BILLABLE_STATUS_BILLABLE}
          {lang}Billable{/lang}
        {elseif $record.billable_status == $smarty.const.BILLABLE_STATUS_PENDING_PAYMENT}
          {lang}Pending Payment{/lang}
        {elseif $record.billable_status == $smarty.const.BILLABLE_STATUS_PAID}
          {lang}Paid{/lang}
        {else}
          {lang}Unknown{/lang}
        {/if}
        </td>
		  </tr>    
		  {/foreach}
		 </tbody>
	  </table>
	{/foreach}
{else}
	<p>{lang}No Time or Expenses logged with this criteria{/lang}</p>	
{/if}