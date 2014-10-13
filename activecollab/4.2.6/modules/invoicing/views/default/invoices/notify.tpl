{title}Resend Email{/title}
{add_bread_crumb}Resend Email{/add_bread_crumb}

<div id="resend_invoice_email">
  {form action=$active_invoice->getNotifyUrl() method=post}
    {wrap_fields}
      {if $active_invoice->isIssued()}
        <p>{lang}Issued On{/lang}: {$active_invoice->getIssuedOn()|date:0}<br>{lang}Payment Due On{/lang}:
          {if $active_invoice->isOverdue()}
            <span class="nok">{$active_invoice->getDueOn()|date:0} ({lang}Overdue{/lang})</span>
          {else}
            {$active_invoice->getDueOn()|date:0}
          {/if}
        </p>

        <div id="issue_invoice_dates">
          {wrap field=due_on}
            {select_date name='issue[due_on]' value=$issue_data.due_on id=issueFormDueOn required=true label='Payment Due On'}
          {/wrap}
        </div>
      {/if}

      <div id="issue_invoice_send_email">
        {label}Resend Email{/label}

        <div>
          {select_company_financial_manager name='issue[user_id]' value=$issue_data.issued_to_id company=$active_invoice->getCompany()}
          <input type="hidden" name="issue[send_emails]" value="1">
        </div>
      </div>
    {/wrap_fields}
   
    {wrap_buttons}
      {submit}Resend Email{/submit}
    {/wrap_buttons}
  {/form}
</div>