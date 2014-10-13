{wrap field="paymentGatewayName" class="payment_user_form"}
  {label for=paymentGatewayName required=yes}Name{/label}
  {text_field name='payment_gateway[additional_properties][name]' value=$payment_gateway_data.name id=paymentGatewayName required=true}
{/wrap}

{wrap field="paymentGatewayAPIUserName" class="payment_user_form"}
  {label for=paymentGatewayAPIUserName required=yes}API Username{/label}
  {text_field name='payment_gateway[additional_properties][api_username]' id='paymentGatewayAPIUserName' value=$payment_gateway_data.api_username required=true}
{/wrap}

{wrap field="paymentGatewayAPIPassword" class="payment_user_form"}
  {label for=paymentGatewayAPIPassword required=yes}API Password{/label}
  {text_field name='payment_gateway[additional_properties][api_password]' value=$payment_gateway_data.api_password id=paymentGatewayAPIPassword required=true}
{/wrap}

{wrap field="paymentGatewayAPISignature" class="payment_user_form"}
  {label for=paymentGatewayAPISignature required=yes}API Signature{/label}
  {text_field name='payment_gateway[additional_properties][api_signature]' value=$payment_gateway_data.api_signature id=paymentGatewayAPISignature required=true}
{/wrap}

{wrap field="paymentGatewayGoLive" class="payment_user_form"}
  {label for=paymentGatewayGoLive required=yes}Go Live{/label}
  <select name='payment_gateway[additional_properties][go_live]' id="paymentGatewayGoLive">
  	<option value='1' {if $payment_gateway_data.go_live}selected="selected"{/if}>Yes</option>
  	<option value='0' {if !$payment_gateway_data.go_live}selected="selected"{/if}>No</option>
  </select>
{/wrap}
{wrap field="additionalInfo" class="payment_user_form"}
	<p class="details">{$additional_info nofilter}</p>
{/wrap}