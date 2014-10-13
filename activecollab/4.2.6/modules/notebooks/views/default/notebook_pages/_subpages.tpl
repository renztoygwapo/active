{if is_foreachable($_subpages)}
<div class="resource object_subpages object_section">
  <div class="head">
    <h2 class="section_name"><span class="section_name_span">{lang}Subpages{/lang}</span></h2>
  </div>
  <div class="body">
    {notebook_pages_tree notebook_pages=$_subpages user=$logged_user}
  </div>
</div>
{/if}