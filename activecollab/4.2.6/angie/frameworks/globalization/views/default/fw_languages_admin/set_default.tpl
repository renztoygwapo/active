{form method="post" action=Router::assemble('admin_languages_set_default')}
  {wrap_fields}
    {wrap field="default_language"}
      {select_default_language name="default_language" value=$default_language}
    {/wrap}
  {/wrap_fields}
  
  {wrap_buttons}
    {submit}Set as Default Language{/submit}
  {/wrap_buttons}
{/form}