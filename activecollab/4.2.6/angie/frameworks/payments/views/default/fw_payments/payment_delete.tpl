{title}Delete Payment{/title}
{add_bread_crumb}Delete Payment{/add_bread_crumb}

{form action="{$active_payment->getDeleteUrl()}" method="POST"}

<div id="main">
	<div class="payment_user_form">
		<label for="payment_status">{lang}Status{/lang}</label>
		{select_payment_status name="payment[status]" items=$status_items selected=$active_payment->getStatus() id="payment_status"}
	</div>
	
	<div class="payment_user_form">
		<label for="payment_status_reason">{lang}Status Reason{/lang}</label>
		{select_payment_status_reason name="payment[status_reason]" selected=$active_payment->getReason() id="payment_status_reason"}
	</div>
	
	<div class="payment_user_form" id="reason_text_box">
		<label for="payment_status_reason_text">{lang}Reason Text{/lang}</label>
		<textarea name="payment[status_reason_text]" id="payment_status_reason_text">{$active_payment->getReasonText()}</textarea>
	</div>
	
	<div class="payment_user_form">
		<label for="payment_note">{lang}Note{/lang}</label>
		<textarea name="payment[note]" readonly="readonly">{$active_payment->getNote()}</textarea>
	</div>
	
	<div class="payment_user_form">
	<input type="submit" value="Delete Payment"/>
	
	</div>

</div>
{/form}