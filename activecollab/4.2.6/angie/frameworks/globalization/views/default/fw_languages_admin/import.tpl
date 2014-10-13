{title}Import Language{/title}
{add_bread_crumb}Upload XML{/add_bread_crumb}

  {if $import_enabled}
    {form method=post action=$import_url id="import_form" enctype="multipart/form-data"}
      {wrap field=xml}
        {label for=xml}Select Language XML File{/label}
        {file_field name=xml id=xml}
      {/wrap}
      
      {wrap_buttons}
        {button type="submit" id="import_btn"}Import{/button}
      {/wrap_buttons}
    {/form}
  {else}
    <p>{lang}Importing is not enabled, please review errors{/lang}</p>
  {/if}
