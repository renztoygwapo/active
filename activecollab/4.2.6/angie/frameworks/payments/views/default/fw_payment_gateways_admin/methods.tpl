{title}Payments Methods{/title}
{add_bread_crumb}Payments Methods{/add_bread_crumb}

<div id="payments_settings_methods">
  {form action=Router::assemble('payment_methods_settings') method=post id="payments_methods_admin"}
    <div class="content_stack_wrapper">
      
    	{foreach $payment_methods as $payment_method}
        <div class="content_stack_element">
          <div class="content_stack_element_info">
            <h3>{$payment_method.label}</h3>
          </div>
          
          <div class="content_stack_element_body">
            {string_list name=$payment_method.name value=$payment_method.value link_title='Add a Method'}
          </div>
        </div>
      {/foreach}
       
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>