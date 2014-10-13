{title}Time and Expenses Reports{/title}
{add_bread_crumb}Filter Time and Expenses{/add_bread_crumb}
{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="tracking_reports" class="filter_criteria">
  <form action="{assemble route=tracking_reports_run}" method="get" class="expanded">
  
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
  App.Wireframe.Events.bind('create_invoice_from_tracking_report.single', function (event, invoice) {
    if (invoice['class'] == 'Invoice') {
      App.Wireframe.Flash.success(App.lang('New invoice created.'));
      App.Wireframe.Content.setFromUrl(invoice['urls']['view']);
    } // if
  });

  $('#tracking_reports').each(function() {
    var wrapper = $(this);
    var currencies_map = {$currencies|json nofilter};
    
    wrapper.filterCriteria({
      'options' : {
        'sum_by_user' : {
          'label' : App.lang('Sum by User'), 
          'selected' : false
        }
      },
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=tracking_reports_export}', response, data, links);

        {if $invoice_based_on_url}
        links.push({
          'text' : App.lang('Create Invoice'), 
          'url' : {$invoice_based_on_url|json nofilter},
          'init' : function() {
            $(this).flyoutForm({
              'title' : App.lang('Create Invoice based on Time Report'),
              'success_event' : 'create_invoice_from_tracking_report'
            });
          }
        });
        {/if}
      }, 
      'criterions' : {
        'type_filter' : {
          'label' : App.lang('Show'), 
          'choices' : {
            'any' : App.lang('Time and Expenses'),
            'time' : App.lang('Time Only'), 
            'expenses' : App.lang('Expenses Only')
          }
        }, 
        'job_type_filter' : {
          'label' : App.lang('Job Type'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'selected' : {
              'label' : App.lang('Selected Types ...'), 
              'prepare' : function(submit_as, criterion, filter, data) {
                if(data['job_types']) {
                  var selected_job_type_ids = typeof(filter) == 'object' && filter && filter['job_type_ids'] ? filter['job_type_ids'] : null;
                  var select_html = '<div class="time_report_' + criterion + '">';

                  App.each(data['job_types'], function(job_type_id, job_type_name) {
                    var id = 'time_report_' + criterion + '_' + job_type_id;
                    var checked = jQuery.isArray(selected_job_type_ids) && selected_job_type_ids.indexOf(parseInt(job_type_id)) >= 0 ? 'checked' : '';

                    select_html += '<div class="job_type"><input type="checkbox" name="' + submit_as + '[job_type_ids][]" value="' + job_type_id + '" id="' + id + '" ' + checked + '> <label for="' + id + '">' + App.clean(job_type_name) + '</label></div>';
                  });

                  $(this).append(select_html + '</div>');
                } else {
                  $(this).text(App.lang('There are no job types to select from'));
                } // if
              }
            }
          }
        },
        'expense_category_filter' : {
          'label' : App.lang('Expense Category'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'selected' : {
              'label' : App.lang('Selected Categories ...'), 
              'prepare' : function(submit_as, criterion, filter, data) {
                if(data['expense_categories']) {
                  var selected_category_ids = typeof(filter) == 'object' && filter && filter['expense_category_ids'] ? filter['expense_category_ids'] : null;
                  var select_html = '<div class="time_report_' + criterion + '">';

                  App.each(data['expense_categories'], function(category_id, category_name) {
                    var id = 'time_report_' + criterion + '_' + category_id;
                    var checked = jQuery.isArray(selected_category_ids) && selected_category_ids.indexOf(parseInt(category_id)) >= 0 ? 'checked' : '';

                    select_html += '<div class="expense_category"><input type="checkbox" name="' + submit_as + '[expense_category_ids][]" value="' + category_id + '" id="' + id + '" ' + checked + '> <label for="' + id + '">' + App.clean(category_name) + '</label></div>';
                  });

                  for(var category_id in data['expense_categories']) {

                  } // for

                  $(this).append(select_html + '</div>');
                } else {
                  $(this).text(App.lang('There are no expense categories to select from'));
                } // if
              }
            }
          }
        }, 
        'user_filter' : {
          'label' : App.lang('Assigned To'), 
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'logged_user' : App.lang('Person Accessing This Report'), 
            'company' : {
              'label' : App.lang('Members of a Company ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['company_id'] : null;
              }
            },
            'selected' : {
              'label' : App.lang('Selected Users ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['user_ids'] : null;
              }
            }
          }
        }, 
        'date_filter' : {
          'label' : App.lang('For Day'), 
          'choices' : {
            'any' : App.lang('Any Day'), 
            'last_month' : App.lang('Last Month'), 
            'last_week' : App.lang('Last Week'), 
            'yesterday' : App.lang('Yesterday'), 
            'today' : App.lang('Today'), 
            'this_week' : App.lang('Week'), 
            'this_month' : App.lang('This Month'), 
            'selected_date' : {
              'label' : App.lang('Selected Date ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'date_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['date_on'] : null;
              }
            }, 
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
        }, 
        'billable_status_filter' : {
          'label' : App.lang('Status'), 
          'choices' : {
            'all' : App.lang('Any'), 
            'not_billable' : App.lang('Non-Billable'), 
            'billable' : App.lang('Billable'), 
            'pending_payment' : App.lang('Pending Payment'), 
            'billable_not_paid' : App.lang('Not Yet Paid (Billable or Pending Payment)'),
            'billable_pending_or_paid' : App.lang('Billable, Pending Payment or Paid'),
            'billable_paid' : App.lang('Already Paid')
          }
        }, 
        'group_by' : {
          'label' : App.lang('Group By'), 
          'choices' : {
            'all' : App.lang("Don't Group"), 
            'date' : App.lang('by Date'), 
            'project' : App.lang('by Project'), 
            'project_client' : App.lang('by Project Client')
          }
        }
      }, 
      'filters' : {$saved_filters|map nofilter},
      'new_filter_url' : '{assemble route=tracking_reports_add}',
      'can_add_filter' : true,
      'on_show_results' : function(response, data, form_data) {
        var results_wrapper = $(this);
        
        // Settings that affect the way results are displayed
        var sum_by_user = false;
        var group_by = 'dont';
        var show_time = true;
        var show_expenses = true;
        
        if(jQuery.isArray(form_data)) {
          for(var i in form_data) {
            if(form_data[i]['name'] == 'filter[sum_by_user]') {
              sum_by_user = form_data[i]['value'] == '1';
            } else if(form_data[i]['name'] == 'filter[group_by]') {
              group_by = form_data[i]['value'];
            } else if(form_data[i]['name'] == 'filter[type_filter]') {
              show_time = form_data[i]['value'] == 'any' || form_data[i]['value'] == 'time';
              show_expenses = form_data[i]['value'] == 'any' || form_data[i]['value'] == 'expenses';
            } // if
          } // for
        } // if
        
        // Display results summarized by user
        if(sum_by_user) {

          App.each(response, function (group_id, group) {
          	if(typeof(group['records']) == 'object' && !jQuery.isEmptyObject(group['records'])) {
              var group_label = typeof(group['label']) == 'string' ? group['label'] : '--';
              var group_wrapper = $('<div class="tracking_report_result_group_wrapper">' +
                '<h2>' + App.clean(group_label) + '</h2>' +
                '<div class="tracking_report_result_group_inner_wrapper"></div>' + 
              '</div>').appendTo(results_wrapper);

              var group_table = $('<table class="common auto summarized" cellspacing="0">' + 
                '<thead><tr><th class="user">' + App.lang('User') + '</th></tr></thead>' +
                '<tbody></tbody>' + 
                '<tfoot><tr><td class="total">' + App.lang('Total') + ':</td></tr></tfoot>' +
              '</table>').appendTo(group_wrapper.find('div.tracking_report_result_group_inner_wrapper'));
              
              if(show_time || show_expenses) {
                var header = group_table.find('thead tr');
                var footer = group_table.find('tfoot tr');
                
                if(show_time) {
                  header.append('<th class="time center">' + App.lang('Time') + '</th>');
                  footer.append('<td class="time center"></td>');
                } // if
                
                if(show_expenses) {
                  for(var currency_id in data['currencies']) {
                    header.append('<th class="expenses center" currency_id="' + currency_id + '">' + App.lang('Expenses (:currency_code)', {
                      'currency_code' : data['currencies'][currency_id]['code']
                    }) + '</th>');
                    footer.append('<td class="expenses center" currency_id="' + currency_id + '"></td>');
                  } // for
                } // if
              } // if
              
              var group_table_body = group_table.find('tbody');

              var total_time = 0;
              var total_expenses = {};

              for(var currency_id in data['currencies']) {
                total_expenses[currency_id] = 0;
              } // for

              App.each(group['records'], function(user_email, record) {
                if(show_time) {
                  total_time += record['time'];
                } // if
                
                if(show_expenses) {
                  for(var currency_id in data['currencies']) {
                    total_expenses[currency_id] += record['expenses_for_' + currency_id];
                  } // for
                } // if
                
                var row = '<tr class="record summarized" user_id="' + record['user_id'] + '" user_email="' + App.clean(user_email) + '">';
                
                row += '<td class="user">' + App.clean(record['user_name']) + '</td>';
                
                if(show_time) {
                  row += '<td class="time center">' + App.hoursFormat(record['time']) + '</td>';
                } // if
                
                if(show_expenses) {
                  for(var currency_id in data['currencies']) {
                    row += '<td class="expenses center" currency_id="' + currency_id + '">' + App.moneyFormat(record['expenses_for_' + currency_id], currencies_map[currency_id]) + '</td>';
                  } // for
                  //row += '<td class="expenses center">' + App.moneyFormat(response[group_id]['records'][user_email]['expenses']) + '</td>';
                } // if
                
                group_table_body.append(row + '</tr>');
              }); // each
              
              if(group_table_body.find('tr.record').length > 1) {
                if(show_time && group_table_body.find('tr.record').length > 1) {
                  footer.find('td.time').text(App.hoursFormat(total_time));
                } // if
              
                if(show_expenses) {
                  for(var currency_id in data['currencies']) {
                    footer.find('td.expenses[currency_id=' + currency_id + ']').text(App.moneyFormat(total_expenses[currency_id], currencies_map[currency_id]));
                  } // for
                } // if
              } else {
                group_table.find('tfoot').remove();
              } // if 
            } // if
          }); // each
          
        // Display a list of records that match given filter criteria
        } else {
          switch(group_by) {
            case 'date':
              var columns = [ App.lang('Value'), App.lang('User'), App.lang('Summary'), App.lang('Status'), App.lang('Project'), App.lang('Action') ]; break;
            case 'project':
              var columns = [ App.lang('Date'), App.lang('Value'), App.lang('User'), App.lang('Summary'), App.lang('Status'), App.lang('Action') ]; break;
            default:
              var columns = [ App.lang('Date'), App.lang('Value'), App.lang('User'), App.lang('Summary'), App.lang('Status'), App.lang('Project'), App.lang('Action') ];
          } // switch

          /**
           * Render record date
           *
           * @param Object record
           * @return string
           */
          var render_date = function(record) {
            return typeof(record['record_date']) == 'object' && record['record_date'] ? App.clean(record['record_date']['formatted_date_gmt']) : '--';
          }; // render_date

          /**
           * Render record user
           *
           * @param Object record
           * @return string
           */
          var render_user = function(record) {
            return typeof(record['user_name']) == 'string' && record['user_name'] ? record['user_name'] : record['user_email'];
          }; // render_user

          /**
           * Render tracked value
           *
           * @param Object record
           * @return string
           */
          var render_value = function(record) {
            if(record['type'] == 'TimeRecord') {
              if(typeof(record['group_name']) != 'undefined' && record['group_name']) {
                return App.lang(':hours of :job_type', {
                  'hours' : App.hoursFormat(record['value']), 
                  'job_type' : record['group_name']
                });
              } else {
                return App.hoursFormat(record['value']);
              } // if
            } else {
              var currency_id = typeof(record['currency_id']) != 'undefined' ? record['currency_id'] : 0;

              if(currency_id && typeof(data['currencies'][currency_id]) == 'object') {
                var currency_code = data['currencies'][currency_id]['code'];
              } else {
                var currency_code = '';
              } // if
              
              if(typeof(record['group_name']) != 'undefined' && record['group_name']) {
                return App.lang(':amount in :category', {
                  'amount' : App.moneyFormat(record['value'], currencies_map[currency_id], null, true),
                  'category' : record['group_name']
                });
              } else {
                return App.moneyFormat(record['value'], currencies_map[currency_id], null, true);
              } // if
            } // if
          }; // render_value

          /**
           * Render time record summary
           *
           * @param Object record
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
           * @param Object record
           * @return string
           */
          var render_status = function(record) {
            var status;
            if(typeof(record) == 'object') {
              status = record['billable_status'];
            } else {
              status = record;
            } //if
            switch(status) {
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
           * Render checkbox
           *
           * @param record
           */
          var render_checkbox = function(record) {
            var type = 'time';
            if(record['type'] == 'Expense') {
              type = 'expense';
            } //if
            return '<input type="checkbox" name="' + type + '" value="' + record['id'] + '"/>';
          }; //render_checkbox

          /**
           * Render project name
           *
           * @param Object record
           * @return string
           */
          var render_project = function(record) {
            return typeof(record['project_name']) == 'string' && record['project_name'] && typeof(record['project_url']) == 'string' && record['project_url'] ? '<a href="' + App.clean(record['project_url']) + '" class="quick_view_item">' + App.clean(record['project_name']) + '</a>' : App.lang('Unknown Project');
          }; // render_project

          App.each(response, function(group_id, group) {
          
            if(jQuery.isArray(group['records']) && group['records'].length) {
              var group_label = typeof(group['label']) == 'string' ? group['label'] : '--';
              var group_wrapper = $('<div class="tracking_report_result_group_wrapper">' +
                '<h2>' + App.clean(group_label) + '</h2>' +
                '<div class="tracking_report_result_group_inner_wrapper"></div>' + 
              '</div>').appendTo(results_wrapper);

              var group_table = $('<table class="common records_list" cellspacing="0">' + 
                '<thead></thead>' +
                '<tbody></tbody>' + 
              '</table>').appendTo(group_wrapper.find('div.tracking_report_result_group_inner_wrapper'));

              var group_table_head = group_table.find('thead');
              var group_table_body = group_table.find('tbody');

              switch(group_by) {
  
                // Render row content for reported grouped by date
                case 'date':
                  group_table_head.append('<tr>' + 
                    '<th class="value">' + App.lang('Value') + '</th>' + 
                    '<th class="user">' + App.lang('User') + '</th>' + 
                    '<th class="summary">' + App.lang('Summary') + '</th>' + 
                    '<th class="status center">' + App.lang('Status') + '</th>' + 
                    '<th class="project right">' + App.lang('Project') + '</th>' +
                    '<th class="action right"><input type="checkbox" id="check_all"/></th>' +
                  '</tr>');
  
                  break;
  
                // Render row content for report grouped by project
                case 'project':
                  group_table_head.append('<tr>' + 
                    '<th class="date left">' + App.lang('Date') + '</th>' + 
                    '<th class="value">' + App.lang('Value') + '</th>' + 
                    '<th class="user">' + App.lang('User') + '</th>' + 
                    '<th class="summary">' + App.lang('Summary') + '</th>' + 
                    '<th class="status center">' + App.lang('Status') + '</th>' +
                    '<th class="action right"><input type="checkbox" id="check_all"/></th>' +
                  '</tr>');
  
                  break;
  
                // Render row content for report that's not grouped or for report that's grouped by project client
                default:
                  group_table_head.append('<tr>' + 
                    '<th class="date left">' + App.lang('Date') + '</th>' + 
                    '<th class="value">' + App.lang('Value') + '</th>' + 
                    '<th class="user">' + App.lang('User') + '</th>' + 
                    '<th class="summary">' + App.lang('Summary') + '</th>' + 
                    '<th class="status center">' + App.lang('Status') + '</th>' + 
                    '<th class="project right">' + App.lang('Project') + '</th>' +
                    '<th class="action right"><input type="checkbox" id="check_all"/></th>' +
                  '</tr>');
  
                  break;
              } // switch

              group_table_head.find('th.action input[type=checkbox]#check_all').click(function() {
                var obj = $(this);
                var records_checkboxes = group_table_body.find('tr.record td.action input[type=checkbox]');
                if(obj.is(':checked')) {
                  records_checkboxes.attr('checked','checked');
                } else {
                  records_checkboxes.removeAttr('checked');
                } //if
              });

              var total_time = 0;
              var total_expenses = {};

							App.each(group['records'], function(record_id, record) {
              
                if(record['type'] == 'TimeRecord') {
                  var record_type = 'time_record';
                  total_time += record['value'];
                } else {
                  var record_type = 'expense';
                  
                  var currency_id = record['currency_id'];

                  if(typeof(total_expenses[currency_id]) == 'undefined') {
                    total_expenses[currency_id] = 0;
                  } // if  

                  total_expenses[currency_id] += record['value'];
                } // if

                // Open row and set properties
                var row = '<tr class="record ' + record_type + '" record_orig_id="'+ record['id']+'" record_id="' + record_id + '" user_id="' + record['user_id'] + '" currency_id="' + record['currency_id'] + '">';

                switch(group_by) {

                  // Render row content for reported grouped by date
                  case 'date':
                    row += '<td class="value">' + render_value(record) + '</td>';
                    row += '<td class="user">' + render_user(record) + '</td>';
                    row += '<td class="summary">' + render_summary(record) +'</td>';
                    row += '<td class="status center">' + render_status(record) + '</td>';
                    row += '<td class="project right">' + render_project(record) + '</td>';
                    row += '<td class="action right">' + render_checkbox(record) + '</td>';

                    break;

                  // Render row content for report grouped by project
                  case 'project':
                    row += '<td class="date left">' + render_date(record) + '</td>';
                    row += '<td class="value">' + render_value(record) + '</td>';
                    row += '<td class="user">' + render_user(record) + '</td>';
                    row += '<td class="summary">' + render_summary(record) +'</td>';
                    row += '<td class="status center">' + render_status(record) + '</td>';
                    row += '<td class="action right">' + render_checkbox(record) + '</td>';

                    break;

                  // Render row content for report that's not grouped or for report that's grouped by project client
                  default:
                    row += '<td class="date left">' + render_date(record) + '</td>';
                    row += '<td class="value">' + render_value(record) + '</td>';
                    row += '<td class="user">' + render_user(record) + '</td>';
                    row += '<td class="summary">' + render_summary(record) +'</td>';
                    row += '<td class="status center">' + render_status(record) + '</td>';
                    row += '<td class="project right">' + render_project(record) + '</td>';
                    row += '<td class="action right">' + render_checkbox(record) + '</td>';

                    break;
                } // switch

                group_table_body.append(row + '</tr>');
              }); // each

              var total_time_string = App.lang('Total Time: :time', {
                'time' : App.hoursFormat(total_time)
              });

              var total_expenses_by_currency = [];

              for(var currency_id in total_expenses) {
                total_expenses_by_currency.push(App.moneyFormat(total_expenses[currency_id], currencies_map[currency_id], null, true));
              } // for

              if(total_expenses_by_currency.length < 1) {
                var total_expenses_string = App.lang('Total Expenses: :expenses', {
                  'expenses' : 0
                });
              } else {
                var total_expenses_string = App.lang('Total Expenses: :expenses', {
                  'expenses' : total_expenses_by_currency.join(', ')
                });
              } // if

              group_table_body.after('<tfoot><tr class="footer_row"><td colspan="' + (columns.length - 1) + '">' + App.clean(total_time_string) + '. ' + App.clean(total_expenses_string) + '</td>' +
                '<td class="action_col right">' +
                  '<select id="change_status_slc">' +
                    '<option value="-1">' + App.lang('Mark selected as:') + '</option>' +
                    '<option value="0">'+ App.lang('Not Billable') +'</option>' +
                    '<option value="1">'+ App.lang('Billable') +'</option>' +
                    '<option value="2">'+ App.lang('Pending Payment') +'</option>' +
                    '<option value="3">'+ App.lang('Paid') +'</option>' +
                  '</select>' +
                '</td></tr></tfoot>');

              var action_select_box = $('tr.footer_row td.action_col select#change_status_slc');
              action_select_box.attr('disabled','disabled');

              group_table_body.find('tr.record td.action input[type=checkbox]').change(function(){
                manage_action_select_box();
              });

              group_table_head.find('th.action input[type=checkbox]#check_all').click(function() {
                manage_action_select_box();
              });

              var manage_action_select_box = function() {
                var selected_checkboxes = group_table_body.find('tr.record td.action input[type=checkbox]:checked');
                if(selected_checkboxes.length > 0) {
                  action_select_box.removeAttr('disabled');
                } else {
                  action_select_box.attr('disabled','disabled');
                } //if
              } //manage_action_select_box
            
              action_select_box.change(function(){
                var obj = $(this);
                if(obj.val() != -1) {
                  var selected_record_checkboxes = group_table_body.find('tr.record td.action input[type=checkbox]:checked');
                  var time_records = [];
                  var expenses = [];

                  App.each(selected_record_checkboxes, function(i, obj){
                    var record = $(obj);
                    var type_name = record.attr('name');
                    if(type_name == 'expense') {
                      expenses.push(record.val());
                    } else {
                      time_records.push(record.val());
                    } //if
                  }); //each

                  var change_status_url = {$change_status_url|json nofilter};
                  $.ajax({
                    url : App.extendUrl(change_status_url, {
                      'skip_layout' : 1
                    }),
                    type : 'post',
                    data : {
                      'data' : {
                        'time_records' : time_records,
                        'expenses' : expenses,
                        'new_status' : obj.val()
                      }
                    },
                    success : function(response) {
                      var new_status = response.new_status;
                      if(response.time_records) {
                        App.each(response.time_records, function(i, id) {
                          var record_row = group_table_body.find('tr.time_record[record_orig_id=' + id + ']');
                          record_row.find('td.status').html(render_status(new_status));
                        }); //each
                      } //if

                      if(response.expenses) {
                        App.each(response.expenses, function(i, id) {
                          var record_row = group_table_body.find('tr.expense[record_orig_id=' + id + ']');
                          record_row.find('td.status').html(render_status(new_status));
                        }); //each
                      } //if
                      App.Wireframe.Flash.success(App.lang('Status changed'));
                    }
                  });
                } //if
                return false;
              });

            } // if
          }); // each
        } // if
      },
      'data' : {
        'companies' : {$companies|map nofilter},
        'users' : {$users|map nofilter},
        'projects' : {$projects|map nofilter},
        'active_projects' : {$active_projects|map nofilter},
        'project_categories' : {$project_categories|map nofilter},
        'job_types' : {$job_types|map nofilter},
        'expense_categories' : {$expense_categories|map nofilter},
        'currencies' : {$currencies|json nofilter}
      }
    });
  });
</script>