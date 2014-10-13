{title lang=false plain=""}{$active_shared_notebook_page->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

<div class="shared_object_details" xmlns="http://www.w3.org/1999/html">
  <div class="icon discussion"></div>

  <div class="name_and_author">
    <h1>{$active_shared_notebook_page->getName()}</h1>
    <span class="action_on_by">{action_on_by format="date" user=$active_shared_notebook_page->getCreatedBy() datetime=$active_shared_notebook_page->getCreatedOn()}</span>
  </div>

  <div class="additional"></div>
</div>

<script type="text/javascript">
  $('.shared_object_details').detach().appendTo($('#public_page_title .public_wrapper').empty());
</script>

<div class="shared_notebook_wrapper">
  <div class="shared_notebook_page_tree">
    <h2><a href="{$active_shared_object->sharing()->getUrl()}">{$active_shared_object->getName()}</a></h2>
    {$active_shared_object->sharing()->renderSubpages() nofilter}
  </div>

  <div class="shared_notebook">
    <h2 class="main_title">{$active_shared_notebook_page->getName()}</h2>
    {if $active_shared_notebook_page->getBody()}
      {$active_shared_notebook_page->getBody()|rich_text nofilter}
    {else}
      <p class="empty_page">{lang}Content not provided{/lang}</p>
    {/if}
  </div>
</div>

{if $active_shared_object->sharing()->supportsComments()}
  {shared_notebook_page_comments object=$active_shared_notebook_page user=$logged_user errors=$errors comment_data=$comment_data}
{/if}