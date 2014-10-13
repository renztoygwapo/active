{if is_foreachable($all_steps)}
  <ul id="indices_rebuild" class="async_process">
  {foreach $all_steps as $url => $text}
    <li class="step" step_url="{$url}">{$text}</li>
  {/foreach}
  </ul>
  
  <script type="text/javascript">
    $('#indices_rebuild').asyncProcess({
      'success' : function() {
        App.widgets.FlyoutDialog.front().close();
        App.Wireframe.Content.reload();
      }
    });
  </script>
{else}
  <p class="empty_page">{lang}Nothing to rebuild{/lang}</p>
{/if}