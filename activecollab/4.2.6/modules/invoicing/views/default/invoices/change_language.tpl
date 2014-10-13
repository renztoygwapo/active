{title}Change Language{/title}
{add_bread_crumb}Change Language{/add_bread_crumb}

<div id="edit_invoice">
  {form action=$active_invoice->getChangeLanguageUrl() method=post}
    {wrap_fields}
      {wrap field=language class=secondHolder}
        {select_language name="invoice[language_id]" value=$invoice_data.language_id label='Language' preselect_default=true}
      {/wrap}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>