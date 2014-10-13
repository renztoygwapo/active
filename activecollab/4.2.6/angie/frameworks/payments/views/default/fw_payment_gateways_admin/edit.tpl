<div id="edit_payment_gateway">
  {form action=$payment_gateway->getEditUrl()}
    {select_payment_gateway user=$logged_user active_payment_gateway=$payment_gateway}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>