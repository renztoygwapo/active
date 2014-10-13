{title}Change Language{/title}
{add_bread_crumb}Change Language{/add_bread_crumb}

<div id="edit_quote">
  {form action=$active_quote->getChangeLanguageUrl() method=post}
    {wrap_fields}
      {wrap field=language class=secondHolder}
        {select_language name="quote[language_id]" value=$quote_data.language_id label='Language' preselect_default=true}
      {/wrap}
    {/wrap_fields}

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>