{use_widget name=inline_tabs module=$smarty.const.ENVIRONMENT_FRAMEWORK}

{if is_foreachable($_smarty_function_inline_tabs)}
  <div class="inline_tabs" id="{$_smarty_function_inline_tabs_id}">
    <div class="inline_tabs_links">
      <ul>
        {foreach $_smarty_function_inline_tabs as $inline_tab_id => $inline_tab}<li><a href="{$inline_tab.url}" id="{$_smarty_function_inline_tabs_id}_{$inline_tab_id}">{$inline_tab.title}{if isset($inline_tab.count)} (<span>{$inline_tab.count}</span>){/if}</a></li>{/foreach}
      </ul>
    </div>
    
    <div class="inline_tabs_content_wrapper">
      <div class="inline_tabs_loader"></div>
      <div class="inline_tabs_content"></div>
    </div>
  </div>
{/if}

<script type="text/javascript">
  App.widgets.InlineTabs.init('{$_smarty_function_inline_tabs_id}');
</script>