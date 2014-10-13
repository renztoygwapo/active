<ul id="resolve_duplicate_ids" class="async_process">
  {foreach $actions as $url => $text}
    <li class="step" step_url="{$url}">{$text}</li>
  {/foreach}
</ul>

<script type="text/javascript">
  $('#resolve_duplicate_ids').asyncProcess({
    'success' : function() {
      App.widgets.FlyoutDialog.front().close();
      App.Wireframe.Content.reload();
    }
  });
</script>