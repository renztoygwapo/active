{use_widget name="my_time_results" module="tracking"}

<div id="my_time_{$widget->getId()}"></div>

<script type="text/javascript">
  $('#my_time_{$widget->getId()}').myTimeResults({
    'records' : {$records|json nofilter},
    'refresh_url' : '{assemble route=my_time_homescreen_widget_refresh widget_id=$widget->getId()}',
    'selected_user_id' : {if $widget->getSelectedUser() instanceof IUser}{$widget->getSelectedUser()->getId()}{else}{$user->getId()}{/if},
    'previous_week_data' : {$widget->getWeekData($user, TrackingReport::DATE_FILTER_LAST_WEEK)|json nofilter},
    'current_week_data' : {$widget->getWeekData($user, TrackingReport::DATE_FILTER_THIS_WEEK)|json nofilter},
    'weekly_time_url' : '{assemble route=my_time_homescreen_widget_weekly_time widget_id=$widget->getId()}',
    'log_time_url' : '{assemble route=my_time_homescreen_widget_add_time widget_id=$widget->getId()}'
  });
</script>