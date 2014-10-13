{title}Update Label{/title}

<div id="update_label">
  {form action=$active_label->getEditUrl()}
    {wrap_fields}
    	{include file=get_view_path('_label_form', 'fw_labels_admin', $smarty.const.LABELS_FRAMEWORK)}
    {/wrap_fields}
    
    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>