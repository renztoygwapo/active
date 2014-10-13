{if !$request->isAsyncCall()}
  <script type="text/javascript">
    App.Wireframe.PageTitle.set({$wireframe->getPageTitle()|json nofilter});
    App.Wireframe.PageTitle.batchSetActions({$wireframe->actions|json nofilter});

    App.Wireframe.PageTitle.setPrintUrl('{$wireframe->print->getUrl()}');
    
    App.Wireframe.BreadCrumbs.batchSet({$wireframe->breadcrumbs|json nofilter});

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