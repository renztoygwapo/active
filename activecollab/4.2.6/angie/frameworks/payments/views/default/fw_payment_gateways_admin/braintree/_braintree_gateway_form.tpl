{wrap field="paymentGatewayName" class="payment_user_form"}
  {label for=paymentGatewayName required=yes}Name{/label}
  {text_field name='payment_gateway[additional_properties][name]' value=$payment_gateway_data.name id=paymentGatewayName required=true}
{/wrap}

{wrap field="paymentGatewayMerchantKey" class="payment_user_form"}
  {label for=paymentGatewayMerchantKey required=yes}Merchant Key{/label}
  {text_field name='payment_gateway[additional_properties][merchant_key]' id='paymentGatewayMerchantKey' value=$payment_gateway_data.merchant_key required=true}
{/wrap}
{wrap field="paymentGatewayPublicKey" class="payment_user_form"}
  {label for=paymentGatewayPublicKey required=yes}Public Key{/label}
  {text_field name='payment_gateway[additional_properties][public_key]' id='paymentGatewayPublicKey' value=$payment_gateway_data.public_key required=true}
{/wrap}
{wrap field="paymentGatewayPrivateKey" class="payment_user_form"}
  {label for=paymentGatewayPrivateKey required=yes}Private Key{/label}
  {text_field name='payment_gateway[additional_properties][private_key]' id='paymentGatewayPrivateKey' value=$payment_gateway_data.private_key required=true}
{/wrap}

{wrap field="paymentGatewayGoLive" class="payment_user_form"}
{label for=paymentGatewayGoLive required=yes}Go Live{/label}
  <select name='payment_gateway[additional_properties][go_live]' id="paymentGatewayGoLive">
    <option value='1' {if $payment_gateway_data.go_live}selected="selected"{/if}>Yes</option>
    <option value='0' {if !$payment_gateway_data.go_live}selected="selected"{/if}>No</option>
  </select>
{/wrap}

{wrap field="paymentGatewayMerchantAccounts" class="payment_user_form"}
  {label for=paymentGatewayMerchantAccounts}Please specify Merchant Account Ids{/label}
  {if is_foreachable($currency_code_map)}
    <table cellpadding="0" cellspacing="2">
    {foreach $currency_code_map as $id => $code}
      <tr>
        <td>{$code}</td>
        <td>=></td>
        <td>{text_field style="width:140px;" name="payment_gateway[additional_properties][merchant_account_ids][$code]" id="curency-$code" value=$payment_gateway_data.merchant_account_ids[$code]}
        </td>
      </tr>
    {/foreach}
      <tr>
        <td colspan="3"><p class="aid" style="width:320px;">{lang}Each merchant account can only process for a single currency. So setting which merchant account to use will also determine which currency the transaction is processed with.{/lang}</p></td>
      </tr>
    </table>
  {/if}
{/wrap}
