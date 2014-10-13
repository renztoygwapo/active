{if !$request->isAsyncCall()}
<script type="text/javascript">
  App.widgets.QuickView.setTitle({$request->isQuickViewCall()|json nofilter}, {$wireframe->getPageTitle()|json nofilter});
  App.widgets.QuickView.batchSetActions({$request->isQuickViewCall()|json nofilter}, {$wireframe->actions|json nofilter});
  App.widgets.QuickView.setPrintUrl({$request->isQuickViewCall()|json nofilter}, {$wireframe->print->getUrl()|json nofilter});

  {if AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()}
    App.Wireframe.Benchmark.set({
      'execution_time' : {BenchmarkForAngie::getTimeElapsed()|number|json nofilter},
      'memory_usage' : {BenchmarkForAngie::getMemoryUsage()|filesize|json nofilter},
      'all_queries' : {BenchmarkForAngie::getQueries()|json nofilter},
      'queries_count' : {BenchmarkForAngie::getQueriesCount()|json nofilter}
    });
  {/if}
</script>
{/if}

{$content_for_layout nofilter}