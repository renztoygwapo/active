{title}Edit Payment{/title}
{add_bread_crumb}Edit Payment{/add_bread_crumb}

<div id="edit_payment">
  {form action=$active_payment->getEditUrl()}
    {wrap_fields}

      {if $active_payment instanceof CustomPayment}
        {wrap field="payment_method"}
          {label for=payment_method required=yes}Method{/label}
          {select_payment_method name="payment[method]" type="custom" value=$active_payment->getMethod()}
        {/wrap}

        {wrap field=paid_on}
          {label for=invoicePaymentPaidOn required=yes}Paid On{/label}
          {select_date  name="payment[paid_on]" id='invoicePaymentPaidOn' required=true value=$active_payment->getPaidOn()}
        {/wrap}
      {/if}



      {wrap field=type}
        {label for=payment_status required=yes}Status{/label}
        {select_payment_status name="payment[status]" selected=$active_payment->getStatus() id="payment_status"}
      {/wrap}

      {wrap field=type}
        {label for=payment_status_reason}Status Reason{/label}
        {select_payment_status_reason name="payment[status_reason]" selected=$active_payment->getReason() id="payment_status_reason"}
      {/wrap}

      {wrap field=type id="reason_text_box"}
        {label for=payment_status_reason_text}Reason Text{/label}
        <textarea name="payment[status_reason_text]" id="payment_status_reason_text" style="width:320px;">{$active_payment->getReasonText()}</textarea>
      {/wrap}

    {wrap field=type id="comment"}
      {label for=payment_comment required=yes}Comment{/label}
      <textarea name="payment[comment]" id="payment_comment" style="width:320px;">{$active_payment->getComment()}</textarea>
    {/wrap}

    {/wrap_fields}

    {wrap_buttons}
      {submit}Edit Payment{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  $('#edit_payment').each(function() {
    var wrapper = $(this);

    var reason = wrapper.find('#payment_status_reason');
    var reason_text_box = wrapper.find('#reason_text_box');

    if(reason.val() == 0) {
      reason_text_box.hide();
    } // if

    reason.change(function () {
      if(reason.val() == 0) {
        reason_text_box.hide('slow');
      } else {
        reason_text_box.show('slow');
      } // if
    });
  });
</script>