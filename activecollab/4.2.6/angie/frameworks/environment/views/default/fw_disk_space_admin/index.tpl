{title}Disk Space Administration{/title}
{add_bread_crumb}Disk Space Administration{/add_bread_crumb}

<div id="disk_space_admin" class="wireframe_content_wrapper settings_panel">
  <div class="settings_panel_header">
    <table class="settings_panel_header_cell_wrapper two_cells">
      <tr>
        <td class="settings_panel_header_cell">
          <h2>{lang}Disk Space Settings{/lang}</h2>
          <div class="properties">
            <div class="property" id="disk_space_admin_limit">
              <div class="label">{lang}Disk Space Limit{/lang}</div>
              <div class="data"></div>
            </div>
            <div class="property" id="disk_space_admin_email_notification">
              <div class="label">{lang}Email Notifications{/lang}</div>
              <div class="data"></div>
            </div>
          </div>

          <ul class="settings_panel_header_cell_actions">
            <li>{link href=Router::assemble('disk_space_admin_settings') mode=flyout_form success_event="disk_usage_updated" title="Disk Space Settings" class="link_button_alternative" id="change_settings_button"}Settings{/link}</li>
          </ul>
        </td>
      </tr>
    </table>

  </div>

  <div class="settings_panel_body">
    <div id="disk_space_chart_wrapper">
      <div id="disk_space_chart"></div>
      <div id="disk_space_chart_overlay"></div>
      <div id="disk_space_chart_label"></div>
    </div>

    <div id="disk_space_legend_wrapper">
      <table id="disk_space_legend"></table>
    </div>
  </div>
</div>

<script type="text/javascript">
  (function () {
    var wrapper = $('#disk_space_admin');
    var initial_disk_usage_data = {$disk_usage_data|json nofilter};
    var change_settings_button = wrapper.find('#change_settings_button');
    var icon_url = App.Wireframe.Utils.imageUrl('icons/12x12/delete.png', 'environment');

    var disk_space_admin_limit = wrapper.find('#disk_space_admin_limit div.data');
    var disk_space_admin_email_notification = wrapper.find('#disk_space_admin_email_notification div.data');
    var disk_space_chart = wrapper.find('#disk_space_chart');
    var disk_space_chart_label = wrapper.find('#disk_space_chart_label');
    var disk_space_legend_table = wrapper.find('#disk_space_legend');

    // handle cleanup tools
    disk_space_legend_table.on('click', 'a.usage_cleanup_button', function () {
      var anchor = $(this);
      var icon = anchor.find('img:first');

      // confirm handler
      if (!anchor.attr('handler') || anchor.attr('handler') == 'confirm') {
        var confirm_message = anchor.attr('confirm_message');
        var success_message = anchor.attr('success_message');

        if (confirm_message && !confirm(confirm_message)) {
          return false;
        } // if

        icon.attr('src', App.Wireframe.Utils.indicatorUrl('small'));

        $.ajax({
          'url' : anchor.attr('href'),
          'type' : 'post',
          'data' : {
            'submitted' : 'submitted'
          },
          'success' : function (response) {
            icon.attr('src', icon_url);
            App.Wireframe.Events.trigger('disk_usage_updated', [response]);
            if (success_message) {
              App.Wireframe.Flash.success(success_message);
            } // if
          },
          'error' : function (response) {
            icon.attr('src', icon_url);
            if (typeof(response) == 'object' && response) {
              if(response['ajax_message']) {
                var response_message = response.ajax_message;
              } else {
                var response_message = App.Wireframe.Utils.responseToErrorMessage(response);
              } // if
            } else {
              var response_message = App.lang('Unknown error occurred. Please try again later');
            } // if
            App.Wireframe.Events.trigger('async_operation_error', [ response_message, response ]);
          }
        });
      } // if

      return false;
    });

    /**
     * Update disk space admin page
     *
     * @param Object disk_usage
     */
    var update_disk_space_admin_page = function(disk_usage) {
      var disk_usage_limit = disk_usage.settings['disk_space_limit'];

      disk_space_admin_limit.text(disk_usage_limit ? App.formatFileSize(disk_usage_limit) : App.lang('No Limit')); // update disk space limit

      if (disk_usage.settings['disk_space_email_notifications']) {
        disk_space_admin_email_notification.text(App.lang('Yes, on :percentage% of disk space', {
          'percentage' : disk_usage.settings['disk_space_low_space_threshold']
        }));
      } else {
        disk_space_admin_email_notification.text(App.lang('Off'));
      } // if

      disk_space_chart.empty(); // empty chart
      disk_space_legend_table.empty().hide(); // empty legend

      if (disk_usage['disk_space_usage']) {
        var chart_data = [];

        // update legend
        disk_space_legend_table.show();
        $.each(disk_usage['disk_space_usage'], function (usage_key, usage) {
          var disk_usage_row = $('<tr><td class="usage_color_code"><span class="usage_color_code_square" style="background-color: ' + usage['color'] + '"></span></td><td class="usage_title">' + App.clean(usage['title']) + '</td><td class="usage_amount">' + App.formatFileSize(usage['size']) + '</td></tr>').appendTo(disk_space_legend_table);
          var cleanup_wrapper = $('<td class="usage_cleanup"></td>').appendTo(disk_usage_row);

          if (usage['cleanup']) {
            cleanup_wrapper.append('<a class="usage_cleanup_button" href="' + usage['cleanup']['url'] + '" title="' + usage['cleanup']['title'] + '" confirm_message="' + usage['cleanup']['confirm_message'] + '" success_message="' + usage['cleanup']['success_message'] + '"><img src="' + icon_url + '" /></a>');
          } else {
            cleanup_wrapper.append('<span class="usage_cleanup_button"><img src="' + icon_url + '" /></span>');
          } // if

          chart_data.push({
            'label' : usage['title'],
            'data'  : Math.ceil(usage['size']),
            'color' : usage['color']
          });
        });

        // add free space to charts and legend if we have limit set
        if (disk_usage_limit) {
          var free_disk_space = disk_usage_limit - disk_usage['total_disk_space_usage'];
          if (free_disk_space < 0) {
            free_disk_space = 0;
          } // if

          disk_space_legend_table.append('<tr><td class="usage_color_code"><span class="usage_color_code_square" style="background-color: #FFF"></span></td><td class="usage_title">' + App.lang('Free Space') + '</td><td class="usage_amount">' + App.formatFileSize(free_disk_space) + '</td></tr>');

          chart_data.push({
            'label' : App.lang('Free Space'),
            'data'  : Math.ceil(free_disk_space),
            'color' : '#FFFFFF'
          });

          disk_space_chart_label.text(Math.floor((disk_usage['total_disk_space_usage'] / disk_usage_limit) * 100) + '%').removeClass('size');
        } else {
          disk_space_chart_label.text(App.formatFileSize(disk_usage['total_disk_space_usage'])).addClass('size');
        } // if

        $.plot(disk_space_chart, chart_data, {
          'series' : {
            'pie' : {
              'stroke' : {
                'color' : '#e6e6e6'
              },
              'innerRadius' : 0.55,
              'show' : true,
              'label' : {
                'show' : false
              }
            }
          },
          'legend' : {
            'show' : false
          }
        });

      } // if
    }; // update_disk_space_admin_page

    // update this page when change event happens
    App.Wireframe.Events.bind('disk_usage_updated.content', function (event, data) {
      update_disk_space_admin_page(data);
    });

    // perform initial update
    update_disk_space_admin_page(initial_disk_usage_data);
  }());
</script>