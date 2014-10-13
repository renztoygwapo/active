  <div class="note_form">
  {wrap field=note_name}
    {label for=note_name required=yes}Invoice Note Name{/label}
    {text_field name='note[name]' value=$note_data.name id=note_name class='title required' required=true}
  {/wrap}
 
  {wrap field=note_content}
    {label for=note_content required=yes}Note Content{/label}
    {textarea_field name='note[content]' id='note_content' class='long required' required=true}{$note_data.content nofilter}{/textarea_field}
    <p class="details boxless">{lang}HTML not supported! Line breaks are preserved.{/lang}</p>
  {/wrap}
  </div>
  <div class="clear"></div>