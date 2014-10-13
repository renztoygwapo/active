{use_widget name="my_time_results" module="tracking"}

<div id="weekly_time">
  <div class="per_day"></div>
  <div class="per_project"></div>
</div>

<script type="text/javascript">

  var selected_user_id = {if $active_homescreen_widget->getSelectedUser() instanceof IUser}{$active_homescreen_widget->getSelectedUser()->getId()}{else}{$logged_user->getId()}{/if};
  var selected_week = {$selected_week|json nofilter};
  var weekly_time_url = '{assemble route=my_time_homescreen_widget_weekly_time widget_id=$active_homescreen_widget->getId()}';

  // Handle grouped by date time records table
  $('#weekly_time .per_day').each(function() {
    var wrapper = $(this);

    var per_day_records = {$per_day_records|json nofilter};
    var week_data = {$week_data|json nofilter};
    var log_time_url = '{assemble route=my_time_homescreen_widget_add_time widget_id=$active_homescreen_widget->getId()}';

    /**
     * Render per day table wireframe
     *
     * @param week_data
     */
    var render_per_day_table_wireframe = function(week_data) {
      var first_week_day = {ConfigOptions::getValueFor('time_first_week_day', $logged_user)};

      var week_days = {
        '0' : App.lang('Sun'),
        '1' : App.lang('Mon'),
        '2' : App.lang('Tue'),
        '3' : App.lang('Wed'),
        '4' : App.lang('Thu'),
        '5' : App.lang('Fri'),
        '6' : App.lang('Sat')
      };

      var first_week_day_found = false;
      var previous_week_days = new Array();

      var week_days_table_header = '<thead><tr>';

      for(var i in week_days) {
        if(i == first_week_day || first_week_day_found) {
          week_days_table_header += '<th>' + week_days[i] + '</th>';
          first_week_day_found = true;
        } else {
          previous_week_days.push(week_days[i]);
        } // if
      } // if

      App.each(previous_week_days, function(k, previous_week_day) {
        week_days_table_header += '<th>' + previous_week_day + '</th>';
      });

      week_days_table_header += '</tr></thead>';

      wrapper.append('<table class="common" cellspacing="0">' +
        week_days_table_header +
        '<tbody>' +
          '<tr>' +
            '<td date="' + week_data.week_day_1 + '">0h</td>' +
            '<td date="' + week_data.week_day_2 + '">0h</td>' +
            '<td date="' + week_data.week_day_3 + '">0h</td>' +
            '<td date="' + week_data.week_day_4 + '">0h</td>' +
            '<td date="' + week_data.week_day_5 + '">0h</td>' +
            '<td date="' + week_data.week_day_6 + '">0h</td>' +
            '<td date="' + week_data.week_day_7 + '">0h</td>' +
          '</tr>' +
        '</tbody>' +
      '</table>');
    }; // render_per_day_table_wireframe

    /**
     * Render per day time results
     *
     * @param results
     */
    var render_per_day_results = function(results) {
      for(var date in results) {
        var records = results[date].records;

        if(typeof(records) == 'object' && !jQuery.isEmptyObject(records)) {
          for(var user_email in records) {
            wrapper.find('table td[date="' + date + '"]').html(App.hoursFormat(records[user_email].time));
          } // for
        } // if
      } // for
    }; // render_per_day_results

    /**
     * Handle per day events
     */
    var handle_per_day_events = function() {
      App.Wireframe.Events.bind('time_record_created.content time_record_deleted.content', function(event, time_record) {
        if(selected_user_id != time_record.user.id) {
          return false;
        } // if

        refresh_per_day_results();
      });

      // Offer add time option
      wrapper.delegate('table td', 'hover', function(event) {
        var time_cell = $(this);

        if(event.type === 'mouseenter') {
          $('<a href="' + App.extendUrl(log_time_url, { week_data : week_data, day_record_date : time_cell.attr('date') }) + '" class="log_time_button"><img src="' + App.Wireframe.Utils.imageUrl('icons/12x12/button-add.png', 'environment') + '" alt="" /></a>').flyoutForm({
            'title' : App.lang('Log Time'),
            'width' : 300,
            'success_event' : 'time_record_created'
          }).appendTo(time_cell);
        } else {
          time_cell.find('a.log_time_button').remove();
        } // if
      });
    }; // handle_per_day_events

    /**
     * Refresh per day time results
     */
    var refresh_per_day_results = function() {
      var refresh_url = App.extendUrl(weekly_time_url, { week : selected_week, refresh : 1 });

      $.ajax({
        'url' : refresh_url,
        'type' : 'get',
        'dataType' : 'json',
        'success' : function(response) {
          render_per_day_results(response.per_day_records);
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to refresh weekly records data');
        }
      });
    }; // refresh_per_day_results

    render_per_day_table_wireframe(week_data);
    render_per_day_results(per_day_records);
    handle_per_day_events();
  });

  // Handle grouped by project time records table
  $('#weekly_time .per_project').each(function() {
    var wrapper = $(this);

    var per_project_records = {$per_project_records|json nofilter};
    var no_records_message = App.lang('No time entries to show');

    /**
     * Render record date
     *
     * @param record
     * @return string
     */
    var render_date = function(record) {
      return typeof(record['record_date']) == 'object' && record['record_date'] ? App.clean(record['record_date']['formatted_date_gmt']) : '--';
    }; // render_date

    /**
     * Render tracked value
     *
     * @param record
     * @return string
     */
    var render_value = function(record) {
      if(typeof(record['group_name']) != 'undefined' && record['group_name']) {
        return App.lang(':hours of :job_type', {
          'hours' : App.hoursFormat(record['value']),
          'job_type' : record['group_name']
        });
      } else {
        return App.hoursFormat(record['value']);
      } // if
    }; // render_value

    /**
     * Render time record summary
     *
     * @param record
     * @return string
     */
    var render_summary = function(record) {
      if(record['parent_type'] == 'Task' && record['parent_name'] && record['parent_url']) {
        var parent_text = '<a href="' + App.clean(record['parent_url']) + '" class="quick_view_item">' + App.clean(record['parent_name']) + '</a>';
      } else {
        var parent_text = '';
      } // if

      if(typeof(record['summary']) == 'string' && record['summary']) {
        var summary_text = App.clean(record['summary']);
      } else {
        var summary_text = '';
      } // if

      if(parent_text && summary_text) {
        return parent_text + ' (' + summary_text + ')';
      } else if(parent_text) {
        return parent_text;
      } else if(summary_text) {
        return summary_text;
      } else {
        return '';
      } // if
    }; // render_summary

    /**
     * Render record status
     *
     * @param record
     * @return string
     */
    var render_status = function(record) {
      switch(record['billable_status']) {
        case 0:
          return App.lang('Not Billable');
        case 1:
          return App.lang('Billable');
        case 2:
          return App.lang('Pending Payment');
        case 3:
          return App.lang('Paid');
        default:
          return App.lang('Unknown Status');
      } // switch
    }; // render_status

    /**
     * Render per project time results
     *
     * @param records
     */
    var render_per_project_results = function(records) {
      if(jQuery.isArray(records) || (typeof(records) == 'object' && records)) {
        App.each(records, function(project_id, project) {
          if(jQuery.isArray(project['records']) && project['records'].length) {
            var project_name = typeof(project['label']) == 'string' ? project['label'] : '--';
            var project_wrapper = $('<div class="project_wrapper">' +
              '<h2>' + App.clean(project_name) + '</h2>' +
              '<div class="project_inner_wrapper"></div>' +
              '</div>').appendTo(wrapper);

            var project_table = $('<table class="common" cellspacing="0">' +
              '<thead>' +
                '<tr>' +
                  '<th>' + App.lang('Date') + '</th>' +
                  '<th>' + App.lang('Value') + '</th>' +
                  '<th>' + App.lang('Summary') + '</th>' +
                  '<th>' + App.lang('Status') + '</th>' +
                '</tr>' +
              '</thead>' +
              '<tbody></tbody>' +
            '</table>').appendTo(project_wrapper.find('div.project_inner_wrapper'));

            var project_table_body = project_table.find('tbody');

            App.each(project['records'], function(record_id, record) {
              project_table_body.append('<tr>' +
                '<td>' + render_date(record) + '</td>' +
                '<td>' + render_value(record) + '</td>' +
                '<td>' + render_summary(record) +'</td>' +
                '<td>' + render_status(record) + '</td>' +
              '</tr>');
            });
          } // if
        });
      } else {
        if(typeof(no_records_message) == 'string') {
          wrapper.append('<p class="empty_page">' + App.clean(no_records_message) + '</p>')
        } // if
      } // if
    }; // render_per_project_results

    /**
     * Handle events
     */
    var handle_per_project_events = function() {
      App.Wireframe.Events.bind('time_record_created.content time_record_deleted.content', function(event, time_record) {
        if(selected_user_id != time_record.user.id) {
          return false;
        } // if

        refresh_per_project_results();
      });
    }; // handle_per_project_events

    /**
     * Refresh per project time results
     */
    var refresh_per_project_results = function() {
      var refresh_url = App.extendUrl(weekly_time_url, { week : selected_week, refresh : 1 });

      $.ajax({
        'url' : refresh_url,
        'type' : 'get',
        'dataType' : 'json',
        'success' : function(response) {
          wrapper.empty();
          render_per_project_results(response.per_project_records);
        },
        'error' : function() {
          App.Wireframe.Flash.error('Failed to refresh weekly records data');
        }
      });
    }; // refresh_per_project_results

    render_per_project_results(per_project_records);
    handle_per_project_events();
  });
</script>