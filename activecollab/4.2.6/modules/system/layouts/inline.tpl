{template_vars_to_js wireframe=$wireframe}
    
<script type="text/javascript">
  App.Wireframe.MainMenu.setCurrent({$wireframe->getCurrentMenuItem()|json nofilter});
  
{if $wireframe->list_mode->isEnabled()}
  App.Wireframe.Content.enableListMode();
{else}
  App.Wireframe.Content.disableListMode();
{/if}
  
  App.Wireframe.BreadCrumbs.batchSet({$wireframe->breadcrumbs|json nofilter});
  App.Wireframe.PageTabs.batchSet({$wireframe->tabs|json nofilter}, {$wireframe->tabs->getCurrentTab()|json nofilter});
  
  App.Wireframe.PageTitle.set({$wireframe->getPageTitle()|json nofilter});
  App.Wireframe.PageTitle.batchSetActions({$wireframe->actions|json nofilter});
  App.Wireframe.PageTitle.setPrintUrl({$wireframe->print->getUrl()|json nofilter});
    
  App.Wireframe.Content.set({$content_for_layout|json nofilter}, {$request->getUrl()|json nofilter});

{if AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()}
  App.Wireframe.Benchmark.set({
    'execution_time' : {BenchmarkForAngie::getTimeElapsed()|number|json nofilter},
    'memory_usage' : {BenchmarkForAngie::getMemoryUsage()|filesize|json nofilter},
    'all_queries' : {BenchmarkForAngie::getQueries()|json nofilter},
    'queries_count' : {BenchmarkForAngie::getQueriesCount()|json nofilter}
  });
{/if}
</script>