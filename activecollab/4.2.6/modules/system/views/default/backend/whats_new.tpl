{title}What's New{/title}
{add_bread_crumb}What's New{/add_bread_crumb}

<div id="whats_new">
  <div id="whats_new_sidebar">
  {if $logged_user instanceof Client || $logged_user instanceof Subcontractor}
    <div id="whats_new_welcome" class="whats_new_sidebar_widget">
      {welcome_message show_title=true}
    </div>
  {/if}

    <div id="whats_new_announcements" class="whats_new_sidebar_widget user_announcements_wrapper">
      <h3 class="head"><span class="head_inner">{lang}Announcements{/lang}</span></h3>
      <div class="body"><div class="body_inner">{user_announcements user=$logged_user}</div></div>
    </div>

    <div id="whats_new_reminders" class="whats_new_sidebar_widget user_reminders_wrapper">
      <h3 class="head"><span class="head_inner">{lang}Reminders{/lang}</span></h3>
      <div class="body"><div class="body_inner">{user_reminders user=$logged_user}</div></div>
    </div>

    <div id="whats_new_my_projects" class="whats_new_sidebar_widget">
      <h3 class="head"><span class="head_inner">{lang}My Projects{/lang}</span></h3>
      <div class="body"><div class="body_inner">{my_projects user=$logged_user}</div></div>
    </div>
  </div>

  <div id="whats_new_recent_activities">
    {activity_log user=$logged_user activity_logs=$activity_logs}
  </div>
</div>

<script type="text/javascript">
  $('#whats_new').each(function() {
    var wrapper = $(this);

    var activities_wrapper = wrapper.find('#whats_new_recent_activities');
    var wireframe_content_height = $('#wireframe_content').height() - 33;

    if(activities_wrapper.height() < wireframe_content_height) {
      activities_wrapper.height(wireframe_content_height + 'px');
    } // if
  });
</script>