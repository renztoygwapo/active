  {wrap_fields}
    {wrap field=body}
      {textarea_field name="code_snippet[body]" id="snippet_code" label='Code'}{$code_snippet_data.body nofilter}{/textarea_field}
    {/wrap}
    
    {wrap field=name class="name"}
      {select_code_syntax name="code_snippet[syntax]" value=$code_snippet_data.syntax label='Code Syntax'}
    {/wrap}
  {/wrap_fields}