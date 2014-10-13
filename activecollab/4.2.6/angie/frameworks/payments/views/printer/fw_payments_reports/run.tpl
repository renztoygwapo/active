{title}Payments Report{/title}

<div id="print_container" class="payments_report_result_group_wrapper">
{if $result}
  {foreach $result as $records_group}
  	{assign var="total_amount" value=array()}
    {if is_foreachable($records_group.records)}
      <h3>{$records_group.label}</h3>
      <table class="common" cellspacing="0">
        <thead>
          <tr>
        		{if $filter->getGroupBy() != PaymentReport::GROUP_BY_DATE}<th class="date">{lang}Date{/lang}</th>{/if}
        		<th class="amount">{lang}Amount{/lang}</th>
            <th class="client">{lang}Client{/lang}</th>
            <th class="invoice">{lang}Invoice{/lang}</th>
            <th class="project_id">{lang}Project Id{/lang}</th>
            <th class="project_name">{lang}Project Name{/lang}</th>
            <th class="gateway">{lang}Gateway{/lang}</th>
            <th class="status">{lang}Status{/lang}</th>
            {if $filter->getIncludeComments()}<th class="comment">{lang}Comment{/lang}</th>{/if}
           
          </tr>
        </thead>
        <tbody>
        {foreach $records_group.records as $record}
        	{$total_amount[$record.currency.id] = $total_amount[$record.currency.id] + $record.amount}
          <tr>
         		{if $filter->getGroupBy() != PaymentReport::GROUP_BY_DATE && $record.record_date}
         	  	<td class="date">{$record.record_date|date:0}</td>
         	  {/if}
         	  <td class="amount">
            	{$record.amount|money} {$record.currency.code}
            </td>
            <td class="client">{$record.client.name}</td>
            <td class="invoice">{$record.parent.name}</td>
            <td class="project_id">{$record.project.id}</td>
            <td class="project_name">{$record.project.name}</td>
            <td class="gateway">
            	{$record.gateway_name}
            </td>
            <td class="status">
            	{$record.status}
            </td>
            {if $filter->getIncludeComments()}<td class="comment">{$record.comment}</td>{/if}
          </tr>
        {/foreach}
        </tbody>
        <tfoot>
        	<td class="total" colspan="10">
        		Total: 
        		{foreach $total_amount as $currency_id => $total}
        			{$total|money} {$currencies.currency_id.code}
        		{/foreach}
        	</td>
        </tfoot>
      </table>
    {/if}
  {/foreach}
{else}
  <p>{lang}Filter returned an empty result{/lang}</p>
{/if}
</div>