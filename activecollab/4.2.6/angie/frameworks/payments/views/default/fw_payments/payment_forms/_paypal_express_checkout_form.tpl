<div class="payment_user_form">
<input type="hidden" name="payment[method]" value="{$payment_gateway->getMethodString()}"/>

{wrap field=type}
  {label for=payment_amount required=yes}{lang}Amount{/lang}{/label}
  {payment_amount name="payment[amount]" id="payment_amount" required=true value="{if $payment_data.amount}{$payment_data.amount}{else}{$active_object->payments()->getAmountToPay()}{/if}" min="0.01" set_as_readonly="{!$active_object->payments()->canMakePartial($logged_user)}" currency=$active_object->payments()->getObjectCurrency()}
{/wrap}


{wrap field=type}
	{label for=comment}Comment{/label}
	<textarea name="payment[comment]" id="comment"></textarea>
{/wrap}



</div>

