{use_widget name="paged_objects_list" module="environment"}
{use_widget name="payment_container" module="payments"}

<div id='render_object_payments'></div>

<script type="text/javascript">
	$('#render_object_payments').paymentContainer('init',{
		'object' : {$active_invoice|json nofilter},
		'payments' : {$active_invoice->payments()->getPayments()|json nofilter}
	});
</script>