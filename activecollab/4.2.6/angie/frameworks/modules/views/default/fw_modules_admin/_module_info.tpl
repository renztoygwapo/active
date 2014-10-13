<div class="module_info">
  <div class="icon"><img src="{$active_module->getIconUrl()}" alt="" /></div>
  <div class="meta">
    <dl>
      <dt>{lang}Name{/lang}:</dt>
      <dd class="module_name">{lang name=$active_module->getDisplayName() version=$active_module->getVersion()}:name, v:version{/lang}</dd>
      
      <dt>{lang}Enabled{/lang}: 
          {if $active_module->isEnabled()}
            {lang}Yes{/lang}
          {else}
            {lang}No{/lang}
          {/if}
      </dt>
      
      <dt>{lang}Description{/lang}:</dt>
      <dd class="module_description">{$active_module->getDescription()}</dd>
    </dl>
  </div>
</div>

<script type="text/javascript">
  App.Wireframe.Events.bind('module_updated.content', function(event, module) {
    App.Wireframe.Content.reload();
  });
</script>