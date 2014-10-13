{if is_foreachable($activity_logs)}
  {activity_log activity_logs=$activity_logs id=recent_activities}

  <p class="recent_activities_rss"><a href="{assemble route=rss}">{lang}Recent Activities{/lang}</a></p>
{else}
  <p class="empty_page"><span class="inner">{lang}There are no activities logged{/lang}</span></p>
{/if}