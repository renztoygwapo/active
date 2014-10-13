{title}Administration{/title}
{add_bread_crumb}Available Administration Tools{/add_bread_crumb}

<div class="admin_panel">
{foreach $admin_panel as $row_name => $row}
  {if $row->hasContent()}
  <div class="admin_panel_row {cycle values='odd,even'}">
    <h3>{$row->getTitle()}</h3>
    {$row->getContent() nofilter}
  </div>
  {/if}
{/foreach}
</div>