{form action=$active_object->category()->getUpdateCategoryUrl() method=post }
  {wrap_fields}
    {wrap field=category_id}
      {select_category name='object[category_id]' value=$object_data.category_id parent=$category_context type=$category_class user=$logged_user label='Category' success_event="category_created"}
    {/wrap}
  {/wrap_fields}
  
  {wrap_buttons}
    {submit}Update Category{/submit}
  {/wrap_buttons}
{/form}