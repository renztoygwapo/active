{title}Custom Payment{/title}
{add_bread_crumb}Custom Payment{/add_bread_crumb}

<div id="main">
<form action="{$active_object->payments()->getCustomAddUrl()}" method="post">
<input type="hidden" name="submitted" value="submitted"/> 
 <div class="comon_values">
 {wrap field=paid_on}
  {label for=invoicePaymentPaidOn required=yes}Paid On{/label}
  {select_date  name="payment[paid_on]" value=$invoice_payment_data.paid_on id=invoicePaymentPaidOn class='required'}
{/wrap}
 
  {wrap field=type}
  {label for=note}Note{/label}
  	<textarea name="payment[note]" id="note"></textarea>
  {/wrap}

  {wrap field=type}
    {label for=payment_amount required=yes}{lang}Amount{/lang}{/label}
    <input type="text" name="payment[amount]" id="payment_amount" class="required" value="{$active_object->payments()->getAmountToPay()|money:$active_object->payments()->getObjectCurrency()}" {if !$active_object->payments()->canMakePartial($logged_user)}readonly='readonly'{/if} />
  {/wrap}

  {wrap field=type}
    {label for=currency required=yes}Currency{/label}
    <select name="payment[currency]" id="currency">
    	<option value="{$active_object->getCurrencyCode()}">{$active_object->getCurrencyCode()}</option>
    </select>
  {/wrap}
  
  {wrap_buttons}
    {submit id="make_payment"}Make Payment{/submit}
  {/wrap_buttons}
 </div>
</form>
</div>
<script type="text/javascript">
$(document).ready(function() { 
	var main_div = $("#main");

	$("#make_payment").click(function(){
		var required_inputs = main_div.find(".required");
    	$(".error_block").hide();
      	var empty_fields = new Object();
      	var i = 0;
    	required_inputs.each (function () {
    		var field = $(this);	
    		if($.trim(field.val()) == '') {
    		  empty_fields[i] = field;
    		  i++;
    		}
    	});
    	if($.isEmptyObject(empty_fields)) {
    		if($.trim($("#payment_amount").val()) == '') {
    			$("#payment_amount").focus();
    			$("#payment_amount").after("<em class='error_block'>* Required</em>");
    			return false;
    		} else {
    			return true;
    		}
    	} else {
    		empty_fields[0].focus();
    		empty_fields[0].after("<em class='error_block'>* Required</em>");
    		return false;
    	}
	});
});
</script>