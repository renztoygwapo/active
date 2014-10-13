<ul id="activity_log_rebuild" class="async_process">
{foreach $actions as $url => $text}
  <li class="step" step_url="{$url}">{$text}</li>
{/foreach}
</ul>

<script type="text/javascript">
  $('#activity_log_rebuild').asyncProcess({
    'success' : function() {
      App.widgets.FlyoutDialog.front().close();
      App.Wireframe.Content.reload();
    }
  });
</script>