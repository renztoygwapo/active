{title}Payments Summary Report{/title}

<div id="print_container" class="payments_summary_report_result_group_wrapper">
{if $result}
  {foreach $result as $year => $records}
  	{assign var="total_amount" value=array()}
    {if is_foreachable($records)}
      <h3>{$year}</h3>
      <table class="common" cellspacing="0">
        <thead>
          <tr>
            <th class="date">{lang}Month{/lang}</th>
        		{foreach $currencies as $curr_id => $currency}
        			<th class="currency">{$currency.code}</th>
        		{/foreach}
        	</tr>
        </thead>
        <tbody>
        
        {foreach $records as $month => $record}
        	<tr>
         		<td class="month">{$month}</td>
         		{foreach $record as $currency_id => $value}
         			<td class="currency">
            		{$value|money}
            	</td>
            	{$total_amount[$currency_id] = $total_amount[$currency_id] + $value}
         		{/foreach}
          </tr>
        {/foreach}
        </tbody>
        <tfoot>
        <th class="total">{lang}Total{/lang}</th>
        {foreach $total_amount as $currency_id => $amount}
        	<td class="total_amount">{$amount|money}</td>
        {/foreach}
        </tfoot>
      </table>
    {/if}
  {/foreach}
{else}
  <p>{lang}Filter returned an empty result{/lang}</p>
{/if}
</div>