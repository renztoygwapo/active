{title}Reports and Filters{/title}
{add_bread_crumb}All{/add_bread_crumb}

<div class="reports_panel">
{foreach $reports_panel as $row_name => $row}
  {if $row->hasContent()}
  <div class="reports_panel_row {cycle values='odd,even'}">
    <h3>{$row->getTitle()}</h3>
    {$row->getContent() nofilter}
  </div>
  {/if}
{/foreach}
</div>