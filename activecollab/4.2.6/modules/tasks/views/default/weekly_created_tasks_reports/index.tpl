{title}Created Tasks (Weekly){/title}
{add_bread_crumb}Created Tasks (Weekly){/add_bread_crumb}
{use_widget name='flot' module=$smarty.const.ENVIRONMENT_FRAMEWORK}
{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="weekly_created_tasks_report" class="filter_criteria">
  <form action="{assemble route=weekly_created_tasks_reports_run}" method="get" class="expanded">

    <!-- Filter Picker -->
    <div class="filter_criteria_head">
      <div class="filter_criteria_head_inner">
        <div class="filter_criteria_picker">
        {lang}Filter{/lang}:
          <select>
            <option value="">{lang}Custom{/lang}</option>
          </select>
        </div>

        <div class="filter_criteria_run">{button type="submit" class="default"}Run{/button}</div>
        <div class="filter_criteria_options" style="display: none"></div>
      </div>
    </div>

    <div class="filter_criteria_body"></div>
  </form>

  <div class="filter_results"></div>
</div>

<script type="text/javascript">
  $('#weekly_created_tasks_report').filterCriteria({
    'on_result_links' : function(response, data, links) {
      App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=weekly_created_tasks_reports_export}', response, data, links);
    },
    'criterions' : {
      'tasks_segment_filter' : {
        'label' : App.lang('Tasks Segment'),
        'choices' : {
          'any' : App.lang('Any'),
          'selected' : {
            'label' : App.lang('Selected Segment ...'),
            'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectTaskSegment']
          }
        }
      },
      'date_filter' : {
        'label' : App.lang('Date Range'),
        'choices' : {
          'any' : App.lang('All Time'),
          'selected_range' : {
            'label' : App.lang('Selected Date Range ...'),
            'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
            'get_name' : function() {
              return 'date';
            },
            'get_value' : function(f) {
              return typeof(f) =='object' && f ? [f['date_from'], f['date_to']] : null;
            }
          }
        }
      },
      'project_filter' : {
        'label' : App.lang('Projects'),
        'choices' : {
          'any' : App.lang('Any Project'),
          'active' : App.lang('Active Projects'),
          'completed' : App.lang('Completed Projects'),
          'category' : {
            'label' : App.lang('From Category ...'),
            'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectProjectCategory']
          },
          'client' : {
            'label' : App.lang('For Client ...'),
            'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
            'get_name' : function(c) {
              return 'project_client_id';
            },
            'get_value' : function(f, c) {
              return typeof(f) == 'object' && f ? f['project_client_id'] : null;
            }
          },
          'selected' : {
            'label' : App.lang('Selected Projects ...'),
            'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectProjects']
          }
        }
      }
    },
    'filters' : {$saved_filters|map nofilter},
    'new_filter_url' : '{assemble route=weekly_created_tasks_reports_add}',
    'can_add_filter' : true,
    'on_show_results' : function(response, data, form_data) {
      var results_wrapper = $(this);

      if(response) {
        var weekly_created_graph = $('<div id="weekly_created_tasks_graph" class="plot centered" style="width: 800px; height: 400px;"></div>').appendTo(results_wrapper);
        var weekly_created_table = $('<table id="weekly_created_tasks_table" class="common auto report_data">' +
          '<thead>' +
          '<tr>' +
          '<td class="week">' + App.lang('Week') + '</td>' +
          '<td class="total_tasks center">' + App.lang('New Tasks') + '</td>' +
          '</tr>' +
          '</thead>' +
          '<tbody></tbody>' +
          '</table>').appendTo(results_wrapper);

        var created_tasks_data = [];
        var rows = '';

        App.each(response, function(k, v) {
          created_tasks_data.push([ v.week_end_timestamp * 1000, v.created_tasks ]);

          rows += '<tr>' +
            '<td class="week">' + App.lang(':year, Week :week', {
            'year' : v.year,
            'week' : v.week
          }) + '</td>' +
            '<td class="created_tasks tasks_count">' + v.created_tasks + '</td>' +
            '</tr>';
        });

        weekly_created_table.find('tbody').append(rows);

        /**
         * Highlight years with different colors
         *
         * @param axes
         * @return Array
         */
        var prepare_grid_marking = function(axes) {
          var markings = [];

          var starts_with = new Date(axes.xaxis.min).setTimezoneOffset(0);
          var reference_date = Date.parse(starts_with.getFullYear() + '/1/1').setTimezoneOffset(0);

          var interval = 1;

          do {
            var from_timestamp = reference_date.getTime();

            reference_date = reference_date.next().year();

            var to_timestamp = reference_date.getTime();

            if(interval % 2) {
              markings.push({
                'xaxis' : {
                  'from' : from_timestamp,
                  'to' : to_timestamp < axes.xaxis.max ? to_timestamp : axes.xaxis.max
                }
              });
            } // if

            interval++;
          } while(to_timestamp < axes.xaxis.max);

          return markings;
        } // prepare_grid_marking

        $.plot(weekly_created_graph, [ {
          'label' : App.lang('Created Tasks'),
          'data' : created_tasks_data
        } ], {
          'series' : {
            'bars' : {
              'show' : true
            }
          },
          'legend' : {
            'noColumns' : 1
          },
          'xaxis' : {
            'mode' : 'time',
            'timeformat': "%d/%m/%y"
          },
          'grid' : {
            'markings' : prepare_grid_marking
          }
        });
      } // if
    },
    'data' : {
      'task_segments' : {$task_segments|json nofilter},
      'companies' : {$companies|json nofilter},
      'projects' : {$projects|map nofilter},
      'active_projects' : {$active_projects|map nofilter},
      'project_categories' : {$project_categories|json nofilter}
    }
  });
</script>