  {form action=$active_object->label()->getUpdateLabelUrl() method=post }
    {wrap_fields}
      {wrap field=label}
        {label for=ObjectLabel}Choose New Label{/label}
        {select_label name="object[label_id]" value=$object_data.label_id id="objectLabel" type=$active_object->label()->getLabelType() user=$logged_user}
      {/wrap}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Update Label{/submit}
    {/wrap_buttons}
  {/form}