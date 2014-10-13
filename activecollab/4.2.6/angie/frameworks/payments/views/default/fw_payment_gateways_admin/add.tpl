<div id="add_payment_gateway">
  {form action=$payment_gateway->getAddUrl()}
    {select_payment_gateway user=$logged_user}
    
    {wrap_buttons}
      {submit}Add Payment Gateway{/submit}
    {/wrap_buttons}
  {/form}
</div>