{title}Make a Payment{/title}
{add_bread_crumb}Make a Payment{/add_bread_crumb}

<div id="add_payment_gateway">
  {form action=$active_object->payments()->getAddUrl()}
  	<div class='content_stack_wrapper autoscrolled'>
    	{make_payment user=$logged_user object=$active_object}
    </div>
    {wrap_buttons}
      {submit}Make payment{/submit}
      {if $active_object->getIssuedTo() instanceof IUser}
				{if $logged_user->isFinancialManager()}     
					{checkbox_field value=1 checked=$invoice_notify_on_payment name="payment[notify_client]" label="Notify {$active_object->getIssuedTo()->getDisplayName()} when this invoice is fully paid"}
      	{else}
      		<input type="hidden" name="payment[notify_client]" value="{$invoice_notify_on_payment}"/>
      	{/if}
      {/if}
    {/wrap_buttons}
  {/form}
</div>