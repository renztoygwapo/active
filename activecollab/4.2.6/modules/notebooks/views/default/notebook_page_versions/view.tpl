<div id="notebook_page_version">
  <div class="notebook_page_version_name">
    <h2>{$active_notebook_page_version->getName()}</h2>
  </div>

  <div class="notebook_page_version_body">
    {if ($active_notebook_page_version->getBody())}
      {$active_notebook_page_version->getBody()|rich_text nofilter}
    {else}
      {lang}No description for this Notebook Page version{/lang}
    {/if}
  </div>
</div>