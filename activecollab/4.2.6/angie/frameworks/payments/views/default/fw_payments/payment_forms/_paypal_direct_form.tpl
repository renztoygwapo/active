<div class="payment_user_form">
  <input type="hidden" name="payment[method]" value="{$payment_gateway->getMethodString()}"/>

{wrap field=name}
  {label for=first_name required=yes}Cardholder First Name{/label}
  {text_field name='payment[first_name]' value=$payment_data.first_name id=first_name required=true}
{/wrap}

{wrap field=type}
  {label for=last_name required=yes}Cardholder Last Name{/label}
  {text_field name='payment[last_name]' value=$payment_data.last_name id=last_name required=true}
{/wrap}

{wrap field=type}
  {label for=credit_card_type required=yes}CC Type{/label}
  {select_credit_card_type cc_types=$payment_gateway->cc_types name='payment[credit_card_type]' value=$payment_data.credit_card_type id=credit_card_type required=true}
{/wrap}

{wrap field=type}
  {label for=credit_card_number required=yes}CC Number{/label}
  {text_field name='payment[credit_card_number]' value=$payment_data.credit_card_number id=credit_card_number required=true}
{/wrap}

{wrap field=type}
  {label for=cc_expiration_date required=yes}CC Expiration Date{/label}
  {select_month name='payment[cc_expiration_month]' id="cc_expiration_month" required=true value=$payment_data.cc_expiration_month}
  {select_year name='payment[cc_expiration_year]' id="cc_expiration_year" required=true value=$payment_data.cc_expiration_year from=$today->getYear()}
  
{/wrap}

{wrap field=type}
  {label for=cc_cvv2_number required=yes}CSC (Card Security Code){/label}
  {text_field name='payment[cc_cvv2_number]' value=$payment_data.cc_cvv2_number id=cc_cvv2_number required=true}
{/wrap}

{wrap field=type}
  {label for=address1 required=yes}Address 1{/label}
  {text_field name='payment[address1]' value=$payment_data.address1 id=address1 required=true}
{/wrap}

{wrap field=type}
  {label for=city required=yes}City{/label}
  {text_field name='payment[city]' value=$payment_data.city id=city required=true}
{/wrap}

{wrap field=type}
  {label for=state required=yes}State{/label}
  {text_field name='payment[state]' value=$payment_data.state id=state required=true}
{/wrap}

{wrap field=type}
  {label for=zip required=yes}Zip{/label}
  {text_field name='payment[zip]' value=$payment_data.zip id=zip required=true}
{/wrap}

{wrap field=type}
  {label for=country required=yes}Country{/label}
  {select_supported_country name='payment[country]' countries=$payment_gateway->countries value=$payment_data.country id=country  required=true}
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


