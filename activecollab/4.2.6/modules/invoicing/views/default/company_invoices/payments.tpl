{title lang=false}{lang name=$active_company->getName()}:name's Payments{/lang}{/title}
{add_bread_crumb}Payments{/add_bread_crumb}

{wrap_columns}
  {wrap_content_column}
    {if is_foreachable($payments)}
      <table class="payments">
        <tr>
          <th class="invoice">{lang}Invoice{/lang}</th>
          <th class="amount">{lang}Amount{/lang}</th>
          <th class="paid_on">{lang}Paid On{/lang}</th>
        </tr>
      {foreach from=$payments item=payment}
        {assign var=payment_invoice value=$payment->getInvoice()}
        <tr class="{cycle values='odd,even'}">
          <td class="invoice">{invoice_link invoice=$payment_invoice company=!$logged_user->isFinancialManager()}</td>
          <td class="amount">{$payment->getAmount()} {$payment_invoice->getCurrencyCode()}</td>
          <td class="paid_on">{$payment->getPaidOn()|date}</td>
        </tr>
      {/foreach}
      </table>
    {else}
      <p class="empty_page"><span class="inner">{lang name=$active_company->getName()}:name has not made any payments{/lang}</span></p>
    {/if}
  {/wrap_content_column}
  
  {wrap_sidebar_column}
    {include file=get_view_path('tabs', 'company_invoices', 'invoicing')}
  {/wrap_sidebar_column}
{/wrap_columns}