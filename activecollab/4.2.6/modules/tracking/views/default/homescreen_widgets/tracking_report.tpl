{use_widget name="tracking_report_results" module="tracking"}

<div id="tracking_report_{$widget->getId()}"></div>

<script type="text/javascript">
  $('#tracking_report_{$widget->getId()}').trackingReportResults({
    'records' : {$records|json nofilter},
    'currencies' : {$currencies|json nofilter},
    'sum_by_user' : {$report->getSumByUser()|json nofilter},
    'show_user_column' : {if $widget->getUserFilter() == TrackingReport::USER_FILTER_LOGGED_USER}false{else}true{/if},
    'group_by' : {$report->getGroupBy()|json nofilter},
    'show_time' : {$report->queryTimeRecords()|json nofilter},
    'show_expenses' : {$report->queryExpenses()|json nofilter}
  });
</script>