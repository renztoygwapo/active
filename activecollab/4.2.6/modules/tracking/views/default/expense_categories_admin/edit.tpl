{title}Update Expense Category{/title}

<div id="update_expense_category">
  {form action=$active_expense_category->getEditUrl()}
  	{wrap_fields}
      {wrap field=name}
        {text_field name="expense_category[name]" value=$expense_category_data.name label="Name" required=true}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>