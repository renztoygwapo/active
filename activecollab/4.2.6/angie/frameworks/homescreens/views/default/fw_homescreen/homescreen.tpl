{title}Configure Custom Home Screen Tabs{/title}
{add_bread_crumb}Custom Home Screen Tab{/add_bread_crumb}

<div id="manage_own_homescreen">
  {configure_homescreen parent=$active_object user=$logged_user id=configure_user_homescreen}
  <p class="empty_page" style="display: none">{lang}No custom tabs defined{/lang}</p>
</div>

<script type="text/javascript">
  $('#manage_own_homescreen').each(function() {
    var wrapper = $(this);

    var configure_home_screen = wrapper.find('#configure_user_homescreen');
    var no_tabs_message = wrapper.find('p.empty_page');

    /**
     * Refresh element visibility
     */
    var refresh_view = function() {
      if(configure_home_screen.find('li.homescreen_tab.real_homescreen_tab').length < 1) {
        configure_home_screen.hide();
        no_tabs_message.show();
      } else {
        no_tabs_message.hide();
        configure_home_screen.show();
      } // if
    } // if

    App.Wireframe.Events.bind('homescreen_tab_added.content homescreen_tab_deleted.content', function(e, tab) {
      refresh_view();
    });

    refresh_view();
  });
</script>