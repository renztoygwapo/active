<div class="payment_user_form">
  <input type="hidden" name="payment[method]" value="{$payment_gateway->getMethodString()}"/>

{wrap field=name}
  {label for=name}Cardholder Name{/label}
  {text_field name='payment[name]' value=$payment_data.name id=name}
{/wrap}

{wrap field=type}
  {label for=credit_card_number required=yes}Credit Card Number{/label}
  {text_field name='payment[credit_card_number]' value=$payment_data.credit_card_number id=credit_card_number required=true}
{/wrap}

{wrap field=type}
  {label for=cc_expiration_date required=yes}Expiration Date{/label}
  {select_month name='payment[cc_expiration_month]' id="cc_expiration_month" required=true value=$payment_data.cc_expiration_month}
  {select_year name='payment[cc_expiration_year]' id="cc_expiration_year" required=true from=$today->getYear() value=$payment_data.cc_expiration_year}

{/wrap}

{wrap field=type}
  {label for=cc_cvc_number}CSC (Card Security Code){/label}
  {text_field name='payment[cc_cvc_number]' value=$payment_data.cc_cvc_number id=cc_cvc_number}
{/wrap}

{wrap field=type}
  {label for=address_line1}Address{/label}
  {text_field name='payment[address_line1]' value=$payment_data.address_line1 id=address_line1}
{/wrap}

{wrap field=type}
  {label for=city}City{/label}
  {text_field name='payment[city]' value=$payment_data.city id=city}
{/wrap}

{wrap field=type}
  {label for=state}State{/label}
  {text_field name='payment[state]' value=$payment_data.state id=state}
{/wrap}

{wrap field=type}
  {label for=zip}Zip{/label}
  {text_field name='payment[zip]' value=$payment_data.zip id=zip}
{/wrap}

{wrap field=type}
  {label for=country}Country{/label}
  {select_supported_country name='payment[country]' countries=$payment_gateway->countries value=$payment_data.country id=country}
{/wrap}

{wrap field=type}
  {label for=payment_amount required=yes}{lang}Amount{/lang}{/label}
  {payment_amount name="payment[amount]" id="payment_amount" required=true value="{if $payment_data.amount}{$payment_data.amount}{else}{$active_object->payments()->getAmountToPay()}{/if}" min="0.01" set_as_readonly="{!$active_object->payments()->canMakePartial($logged_user)}" currency=$active_object->payments()->getObjectCurrency()}
{/wrap}

{wrap field=type}
	{label for=comment}Comment{/label}
	<textarea name="payment[comment]" id="comment"></textarea>
{/wrap}




</div>


