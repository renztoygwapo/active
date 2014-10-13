{title}Invoices{/title}

<div id="print_container">
{if $result}
  {foreach $result as $records_group_name => $records_group}
    {if is_foreachable($records_group.invoices)}
      {if $records_group_name != 'all'}
      <h3>{$records_group.label}</h3>
      {/if}

      <table class="common" cellspacing="0">
        <thead>
          <tr>
            <th class="invoice">{lang}Invoice{/lang}</th>
            <th class="status center">{lang}Status{/lang}</th>
            <th class="client">{lang}Client{/lang}</th>
            <th class="project">{lang}Project{/lang}</th>
            <th class="due_on">{lang}Due On{/lang}</th>
            <th class="amount_due right">{lang}Balance Due{/lang}</th>
            <th class="total right">{lang}Total{/lang}</th>
          </tr>
        </thead>
        <tbody>
        {foreach $records_group.invoices as $record}
          {if $record.balance_due}
            {assign_var name=formatted_balance_due}{$record.balance_due|money:$currencies.{$record.currency_id}:$logged_user->getLanguage():true:true}{/assign_var}
          {else}
            {assign_var name=formatted_balance_due}--{/assign_var}
          {/if}

          {if $record.total > 0}
            {assign_var name=formatted_total}{$record.total|money:$currencies.{$record.currency_id}:$logged_user->getLanguage():true:true}{/assign_var}
          {else}
            {assign_var name=formatted_total}--{/assign_var}
          {/if}

          <tr>
            <td class="invoice">{if $record.status > 0}#{/if}{$record.name}</td>
            <td class="status center">
            {if $record.status === $smarty.const.INVOICE_STATUS_DRAFT}
              {lang}Draft{/lang}
            {elseif $record.status === $smarty.const.INVOICE_STATUS_ISSUED}
              {lang}Issued{/lang}
            {elseif $record.status === $smarty.const.INVOICE_STATUS_PAID}
              {lang}Paid{/lang}
            {elseif $record.status === $smarty.const.INVOICE_STATUS_CANCELED}
              {lang}Canceled{/lang}
            {else}
              {lang}Unknown{/lang}
            {/if}
            </td>
            <td class="client">
              {if $record.client && $record.client.name}
                {$record.client.name}
              {else}
                --
              {/if}
            </td>
            <td class="project">
              {if $record.project && $record.project.id && $record.project.name}
                {$record.project.name}
              {else}
                {lang}Not Set{/lang}
              {/if}
            </td>
            <td class="due_on">
              {if $record.due_on instanceof DateValue}
                {$record.due_on|date:0}
              {else}
                --
              {/if}
            </td>
            <td class="amount_due right">{$formatted_balance_due}</td>
            <td class="total right">{$formatted_total}</td>
          </tr>
        {/foreach}
        </tbody>
        <tfoot>
          <tr>
            <td colspan="5" class="right bold">{lang}Total{/lang}:</td>
            <td class="total_due right">
              {foreach $records_group['total_due'] as $currency_id => $total_due}
                {$total_due|money:$currencies.{$currency_id}:$logged_user->getLanguage():true:true}{if not $total_due@last} &middot; {/if}
              {/foreach}
            </td>
            <td class="total_invoiced right">
              {foreach $records_group['total'] as $currency_id => $total_due}
                {$total_due|money:$currencies.{$currency_id}:$logged_user->getLanguage():true:true}{if not $total_due@last} &middot; {/if}
              {/foreach}
            </td>
            </td>
          </tr>
        </tfoot>
      </table>
    {/if}
  {/foreach}
{else}
  <p>{lang}Filter returned an empty result{/lang}</p>
{/if}
</div>