{form action=$add_code_snippet_url class="code_snippets_form"}
  {include file=get_view_path('_code_snippet_form', 'fw_code_snippets', $smarty.const.VISUAL_EDITOR_FRAMEWORK)}
    
  {wrap_buttons}
    {submit}Insert Code{/submit}
  {/wrap_buttons}
{/form}