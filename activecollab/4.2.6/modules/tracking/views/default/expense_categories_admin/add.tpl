{title}New Expense Category{/title}

<div id="new_expense_category">
  {form action=Router::assemble('expense_categories_add')}
    {wrap_fields}
      {wrap field=name}
        {text_field name="expense_category[name]" value=$expense_category_data.name label="Name" required=true}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Create Expense Category{/submit}
    {/wrap_buttons}
  {/form}
</div>