{title report_name=$filter_name}Time and Expenses Report (:report_name){/title}

<div id="print_container">
{if $result}
  {foreach $result as $records_group}
    {if is_foreachable($records_group.records)}
      <h3>{$records_group.label}</h3>
      <table class="common" cellspacing="0">
        <thead>
          <tr>
        	{if $filter->getSumByUser()}
            <th class="user" style="width: 20%">{lang}User{/lang}</th>
            {if $filter->queryTimeRecords()}<th class="time" style="width: 10%">{lang}Time{/lang}</th>{/if}
            {if $filter->queryExpenses()}
              {foreach $currencies as $currency}
                <th class="expenses">{lang currency=$currency->getCode()}Expenses (:currency){/lang}</th>
              {/foreach}
            {/if}
        	{else}
        	  {if $filter->getGroupBy() != TrackingReport::GROUP_BY_DATE}<th class="date">{lang}Date{/lang}</th>{/if}
            <th class="value">{lang}Value{/lang}</th>
            <th class="user">{lang}User{/lang}</th>
            <th class="summary">{lang}Summary{/lang}</th>
            <th class="status">{lang}Status{/lang}</th>
            {if $filter->getGroupBy() != TrackingReport::GROUP_BY_PROJECT}<th class="project">{lang}Project{/lang}</th>{/if}
        	{/if}
          </tr>
        </thead>
        <tbody>
        {foreach $records_group.records as $record}
          <tr>
          {if $filter->getSumByUser()}
            <td class="user">{$record.user_name}</td>
          	{if $filter->queryTimeRecords()}<td class="time" style="width: 10%">{$record.time|hours}h</td>{/if}
          	{if $filter->queryExpenses()}
              {foreach $currencies as $currency_id => $currency}
                {assign_var name=expenses_for_currency}expenses_for_{$currency_id}{/assign_var}
                <td class="expenses">{$record.$expenses_for_currency|money:$currency}</td>
              {/foreach}
            {/if}
         	{else}
         		{if $filter->getGroupBy() != TrackingReport::GROUP_BY_DATE}
         	  <td class="date">{$record.record_date|date:0}</td>
         	  {/if}
            <td class="value">
            {if $record.type == 'TimeRecord'}
            	{if $record.group_name}
            	  {lang hours=$record.value|hours job_type=$record.group_name}:hours of :job_type{/lang}
            	{else}
            	  {$record.value|hours}h
            	{/if}
            {else}
              {assign_var name=formatted_currency}{$record.value|money:$currencies.{$record.currency_id}:$logged_user->getLanguage():true:true}{/assign_var}

            	{if $record.group_name}
            	  {lang amount=$formatted_currency category=$record.group_name}:amount in :category{/lang}
            	{else}
            	  {$formatted_currency}
            	{/if}
            {/if}
            </td>
            <td class="user" style="width: 20%">{$record.user_name}</td>
            <td class="summary">
            {if ($record.parent_type == 'Task' && $record.parent_name) && $record.summary}
              {lang name=$record.parent_name}Task: :name{/lang} ({$record.summary})
            {elseif $record.parent_type == 'Task' && $record.parent_name}
              {lang name=$record.parent_name}Task: :name{/lang}
            {elseif $record.summary}
              {$record.summary}
            {/if}
            </td>
            <td class="status">
            {if $record.billable_status == $smarty.const.BILLABLE_STATUS_NOT_BILLABLE}
              {lang}Not Billable{/lang}
            {elseif $record.billable_status == $smarty.const.BILLABLE_STATUS_BILLABLE}
              {lang}Billable{/lang}
            {elseif $record.billable_status == $smarty.const.BILLABLE_STATUS_PENDING_PAYMENT}
              {lang}Pending Payment{/lang}
            {else $record.billable_status == $smarty.const.BILLABLE_STATUS_PAID}
              {lang}Paid{/lang}
            {/if}
            </td>
            {if $filter->getGroupBy() != TrackingReport::GROUP_BY_PROJECT}<td class="project">{$record.project_name}</td>{/if}
          {/if}
          </tr>
        {/foreach}
        </tbody>
        <tfoot>
          <tr>
        {if $filter->getSumByUser()}
          <td style="width: 20%">{lang}Total{/lang}:</td>
          {if $filter->queryTimeRecords()}<td style="width: 10%">{$records_group['total_time']|hours}h</td>{/if}
          {if $filter->queryExpenses()}
            {foreach $currencies as $currency_id => $currency}
              <td>{$records_group.total_expenses.$currency_id.verbose}</td>
            {/foreach}
          {/if}
        {else}
          <td colspan="{if $filter->getGroupBy() == TrackingReport::GROUP_BY_DATE || $filter->getGroupBy() == TrackingReport::GROUP_BY_PROJECT}5{else}6{/if}">
          {if $records_group['total_time']}
            {lang}Total Time{/lang}: {$records_group['total_time']|hours}h.
          {/if}

          {if $records_group['has_expenses']}
            {lang}Total Expenses{/lang}:
            {foreach $currencies as $currency_id => $currency_details}
              {if $records_group.total_expenses.$currency_id.value}
                {$records_group.total_expenses.$currency_id.verbose}
              {/if}
            {/foreach}
          {/if}
          </td>
        {/if}
          </tr>
        </tfoot>
      </table>
    {/if}
  {/foreach}
{else}
  <p>{lang}Filter returned an empty result{/lang}</p>
{/if}
</div>