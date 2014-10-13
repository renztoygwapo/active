
{wrap field="payment_method"}
	{label for=payment_method required=yes}Method{/label}
	{select_payment_method name="payment[method]" type="custom"}
{/wrap}

{wrap field=type}
  {label for=payment_amount required=yes}{lang}Amount{/lang}{/label}
  {payment_amount name="payment[amount]" id="payment_amount" required=true value="{$active_object->payments()->getAmountToPay()}" min="0.01" set_as_readonly="{!$active_object->payments()->canMakePartial($logged_user)}" currency=$active_object->payments()->getObjectCurrency()}
{/wrap}

{wrap field=paid_on}
  {label for=invoicePaymentPaidOn required=yes}Paid On{/label}
  {select_date  name="payment[paid_on]" id=invoicePaymentPaidOn required=true value=$today->getForUser($logged_user)}
{/wrap}


{wrap field=type}
	{label for=comment}Comment{/label}
	<textarea name="payment[comment]" id="comment"></textarea>
{/wrap}


