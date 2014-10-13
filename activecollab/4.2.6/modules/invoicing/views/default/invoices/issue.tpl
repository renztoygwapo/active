{title}Issue Invoice{/title}
{add_bread_crumb}Issue{/add_bread_crumb}

<div id="issue_invoice">
  {form action=$active_invoice->getIssueUrl() method=post}
    {wrap_fields}
      <div id="issue_invoice_dates">
  	      {wrap field=issued_on}
  	        {select_date name='issue[issued_on]' value=$issue_data.issued_on required=true label='Issued On'}
  	      {/wrap}

  	      {wrap field=due_in_days}
  	        {select_invoice_due_on name='issue[due_in_days]' value=$issue_data.due_in_days required=true label='Payment Due On'}
  	      {/wrap}
	    </div>

      <div id="issue_invoice_send_email">
        {label}Notify Client{/label}

        <p><input type="radio" name="issue[send_emails]" value="1" {if $issue_data.send_emails}checked="checked"{/if} class="send_mails_radio inline input_radio" id="issueFormSendEmailsYes"> {label for="issueFormSendEmailsYes" main_label=false after_text=''}Send email to client{/label}</p>
        <div id="select_invoice_recipients" style="display: none">
          {select_company_financial_manager name='issue[user_id]' value=$issue_data.user_id company=$active_invoice->getCompany()}
          <p id="issue_invoice_send_email_pdf">{lang}PDF version of the invoice will be attached to the email{/lang}</p>
        </div>
        <p><input type="radio" name="issue[send_emails]" value="0" {if !$issue_data.send_emails}checked="checked"{/if} class="send_mails_radio inline input_radio" id="issueFormSendEmailsNo"> {label for="issueFormSendEmailsNo" main_label=false after_text=''}Don't send emails, but mark invoice as issued{/label}</p>

        <script type="text/javascript">
          $("#issue_invoice .send_mails_radio").click(function(){
            if($(this).val() == '1') {
              $("#select_invoice_recipients").slideDown();
            } else {
              $("#select_invoice_recipients").slideUp();
            }//if
          });
        </script>
      </div>
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Issue Invoice{/submit}
    {/wrap_buttons}
  {/form}
</div>