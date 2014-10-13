{title}New Label{/title}

<div id="new_label">
  {form action=$add_label_url}
  	{wrap_fields}
    	{include file=get_view_path('_label_form', 'fw_labels_admin', $smarty.const.LABELS_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Create Label{/submit}
    {/wrap_buttons}
  {/form}
</div>