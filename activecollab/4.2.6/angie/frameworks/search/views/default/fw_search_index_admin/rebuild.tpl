<ul id="search_index_rebuild" class="async_process">
{foreach $active_search_index->getRebuildSteps() as $rebuild_step}
  <li class="step" step_url="{$rebuild_step.url}">{$rebuild_step.text}</li>
{/foreach}
</ul>

<script type="text/javascript">
  $('#search_index_rebuild').asyncProcess({
    'success' : function() {
      App.widgets.FlyoutDialog.front().close();
      App.Wireframe.Content.reload();
    }
  });
</script>