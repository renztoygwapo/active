{wrap field="paymentGatewayName" class="payment_user_form"}
  {label for=paymentGatewayName required=yes}Name{/label}
  {text_field name='payment_gateway[additional_properties][name]' value=$payment_gateway_data.name id=paymentGatewayName required=true}
{/wrap}

{wrap field="paymentGatewayAPIKey" class="payment_user_form"}
  {label for=paymentGatewayAPIKey required=yes}API Key{/label}
  {text_field name='payment_gateway[additional_properties][api_key]' id='paymentGatewayAPIKey' value=$payment_gateway_data.api_key required=true}
{/wrap}
