{title}Payments Reports{/title}
{add_bread_crumb}Filter Payments{/add_bread_crumb}
{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="payments_reports" class="filter_criteria">
  <form action="{assemble route=payments_reports_run}" method="get" class="expanded">
  
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
  $('#payments_reports').each(function() {
    var wrapper = $(this);
    var currencies_map = {$currencies|json nofilter};

    wrapper.filterCriteria({
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=payments_reports_export}', response, data, links);
			}, 
			'options' : {
        'include_comments' : {
          'label' : App.lang('Include Comments'), 
          'selected' : false
        }
      },
      'criterions' : {
        'company_filter' : {
          'label' : App.lang('Client'), 
          'choices' : {
            'any' : App.lang('Any'),
            'selected' : {
                'label' : App.lang('Selected Client ...'), 
                'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
                'get_name' : function(c) {
                  return 'company_id';
                },
                'get_value' : function(f, c) {
                  return typeof(f) == 'object' && f ? f['company_id'] : null;
                }
              } 
          }
        },
        'date_filter' : {
          'label' : App.lang('Payment Date'), 
          'choices' : {
            'any' : App.lang('Any Day'), 
            'last_month' : App.lang('Last Month'), 
            'last_week' : App.lang('Last Week'), 
            'yesterday' : App.lang('Yesterday'), 
            'today' : App.lang('Today'), 
            'this_week' : App.lang('Week'), 
            'this_month' : App.lang('This Month'),
            'this_year' : App.lang('This Year'),
            'last_year' : App.lang('Last Year'),
            'selected_year' : {
              'label' : App.lang('Selected Year ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectYear'],
              'get_name' : function(c) {
                return 'year';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['year'] : null;
              }
            }, 
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
        'payment_status_filter' : {
          'label' : App.lang('Payment Status'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'selected' : {
              'label' : App.lang('Selected Status ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectPaymentStatus'],
              'get_name' : function(c) {
                return 'payment_status_selected';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['payment_status_selected'] : null;
              }
            } 
          }
        },
        'group_by' : {
          'label' : App.lang('Group By'), 
          'choices' : {
            'all' : App.lang("Don't Group"), 
            'date' : App.lang('by Date'), 
            'month' : App.lang('by Month'), 
            'year' : App.lang('by Year'),
            'client' : App.lang('by Client')
          }
        }
      }, 
      'filters' : {$saved_filters|map nofilter},
      'new_filter_url' : '{assemble route=payments_reports_add}',
      'can_add_filter' : true,
      'on_show_results' : function(response, data, form_data) {
        var results_wrapper = $(this);
        var group_by = 'dont'; // Settings that affect the way results are displayed
        var include_comments = false;
        
        if(jQuery.isArray(form_data)) {
          App.each(form_data, function(k, form_var) {
            if(form_var['name'] == 'filter[group_by]') {
              group_by = form_var['value'];
            }//if

            if(form_var['name'] == 'filter[include_comments]') {
              include_comments = form_var['value'] == '1';
            } // if
          });
        } // if
        
        /**
         * Render record date
         *
         * @param Object record
         * @return string
         */
        var render_date = function(record) {
          return typeof(record['created_on']) == 'object' && record['created_on'] ? App.clean(record['created_on']['formatted_date_gmt']) : '--';
        }; // render_date


        /**
         * Render invoice number
         *
         * @param Object record
         * @return string
         */
        var render_invoice_number = function(record) {
          return typeof(record['parent']) == 'object' && record['parent'] ? '<a href="' + record['parent']['view_url'] + '">' + App.clean(record['parent']['name']) + '</a>' : '--';
        }; // render_date

        /**
         * Render project name
         *
         * @param Object record
         * @return string
         */
        var render_project_name = function(record) {
          return typeof(record['project']) == 'object' && record['project']['id'] ? '<a href="' + record['project']['view_url'] + '">' + App.clean(record['project']['name']) + '</a>' : '--';
        }; // render_project_name

        /**
         * Render project id
         *
         * @param Object record
         * @return string
         */
        var render_project_id = function(record) {
          return typeof(record['project']) == 'object' && record['project']['id'] ? record['project']['id'] : '--';
        }; // render_project_id

        /**
         * Render paid on date
         *
         * @param Object record
         * @return string
         */
        var render_paid_date = function(record) {
          return typeof(record['paid_on']) == 'object' && record['paid_on'] ? App.clean(record['paid_on']['formatted_date_gmt']) : '--';
        }; // render_paid_date

        /**
         * Render record user
         *
         * @param Object record
         * @return string
         */
        var render_client = function(record) {
          return typeof(record['client']) == 'object' && record['client'] && record['client']['name'] ? '<a href="' + record['client']['view_url'] + '">' + App.clean(record['client']['name']) + '</a>' : App.lang('Unknown Client');
        }; // render_user

        /**
         * Render payment amount
         *
         * @param Object record
         * @return string
         */
        var render_amount = function(record) {
          if(typeof(record['amount']) == 'number' && record['amount']) {
            if(typeof(record['currency']) == 'object') {
              return App.moneyFormat(record['amount'], currencies_map[record['currency_id']], null, true);
            } else {
              return App.moneyFormat(record['amount']);
            } // if
          }//if
        }; // render_amount

        /**
         * Render record status
         *
         * @param Object record
         * @return string
         */
        var render_status = function(record) {
          return typeof(record['status']) == 'string' && record['status'] ? App.clean(record['status']) : App.lang('Unknown Status');
        }; // render_status

        /**
         * Render record comment
         *
         * @param Object record
         * @return string
         */
        var render_comment = function(record) {
          return typeof(record['comment']) == 'string' && record['comment'] ? App.clean(record['comment']) : '--';
        }; // render_status

        /**
         * Render gateway type
         *
         * @param Object record
         * @return string
         */
        var render_gateway = function(record) {
          return typeof(record['gateway_name']) == 'string' && record['gateway_name'] ? App.clean(record['gateway_name']) : App.lang('Unknown Gateway');
        }; // render_gateway

        // Render records
        App.each(response, function(group_id, group) {

          if(jQuery.isArray(group['records']) && group['records'].length) {
            var group_label = typeof(group['label']) == 'string' ? group['label'] : '--';
            var group_wrapper = $('<div class="payments_report_result_group_wrapper">' +
              '<h2>' + App.clean(group_label) + '</h2>' +
              '<div class="payments_report_result_group_inner_wrapper"></div>' +
            '</div>').appendTo(results_wrapper);

            var group_table = $('<table class="common records_list" cellspacing="0">' +
              '<thead></thead>' +
              '<tbody></tbody>' +
            '</table>').appendTo(group_wrapper.find('div.payments_report_result_group_inner_wrapper'));

            var group_table_head = group_table.find('thead');
            var group_table_body = group_table.find('tbody');

            if(group_by == 'date' || group_by == 'month' || group_by == 'year') {
              group_table_head.append('<tr>' +
                '<th class="amount">' + App.lang('Amount') + '</th>' +
                '<th class="client">' + App.lang('Client') + '</th>' +
                '<th class="invoice">' + App.lang('Invoice') + '</th>' +
                '<th class="project_name">' + App.lang('Project') + '</th>' +
                '<th class="gateway">' + App.lang('Payment Method') + '</th>' +
                '<th class="status">' + App.lang('Status') + '</th>' +
              '</tr>');
            } else {
              group_table_head.append('<tr>' +
                '<th class="date left">' + App.lang('Paid On') + '</th>' +
                '<th class="amount">' + App.lang('Amount') + '</th>' +
                '<th class="client">' + App.lang('Client') + '</th>' +
                '<th class="invoice">' + App.lang('Invoice') + '</th>' +
                '<th class="project_name">' + App.lang('Project') + '</th>' +
                '<th class="gateway">' + App.lang('Payment Method') + '</th>' +
                '<th class="status">' + App.lang('Status') + '</th>' +
              '</tr>');
            } // if

            if(include_comments) {
              group_table_head.find('tr th.status').after('<th class="comment">' + App.lang('Comment') + '</th>');
            }//if

            var columns_num = group_table_head.find('tr th').length;

            var total_amount = {};

            App.each(group['records'], function(record_id, record) {
              var record_type = 'payment';
              var currency_id = record['currency_id'];
              if(typeof(total_amount[currency_id]) == 'undefined') {
                total_amount[currency_id] = 0;
              } // if
              total_amount[currency_id] += record['amount'];

              // Open row and set properties
              var row = '<tr class="' + record_type + '" record_id="' + record_id + '" currency_id="' + record['currency_id'] + '">';

              if(group_by == 'date' || group_by == 'month' || group_by == 'year') {
                row += '<td class="amount">' + render_amount(record) + '</td>';
                row += '<td class="client quick_view_item">' + render_client(record) + '</td>';
                row += '<td class="invoice quick_view_item">' + render_invoice_number(record) + '</td>';
                row += '<td class="project_name quick_view_item">' + render_project_name(record) + '</td>';
                row += '<td class="gateway">' + render_gateway(record) +'</td>';
                row += '<td class="status">' + render_status(record) + '</td>';
              } else {
                row += '<td class="date left">' + render_paid_date(record) + '</td>';
                row += '<td class="amount">' + render_amount(record) + '</td>';
                row += '<td class="client quick_view_item">' + render_client(record) + '</td>';
                row += '<td class="invoice quick_view_item">' + render_invoice_number(record) + '</td>';
                row += '<td class="project_name quick_view_item">' + render_project_name(record) + '</td>';
                row += '<td class="gateway">' + render_gateway(record) +'</td>';
                row += '<td class="status">' + render_status(record) + '</td>';
              } // if

              if(include_comments) {
                row += '<td class="comment">' + render_comment(record) + '</td>';
              }//if

              group_table_body.append(row + '</tr>');
            }); // each

            var total_payment_by_currency = [];

            for(var currency_id in total_amount) {
              var currency = typeof(data['currencies'][currency_id]) == 'object' ? data['currencies'][currency_id] : null;
              total_payment_by_currency.push(App.moneyFormat(total_amount[currency_id], currency, null, currency ? true : false));
            } // for

            if(total_payment_by_currency.length < 1) {
              var total_amount_string = App.lang('Total Amount: :amount', {
                'amount' : 0
              });
            } else {
              var total_amount_string = App.lang('Total Amount: :amount', {
                'amount' : total_payment_by_currency.join(', ')
              });
            } // if

            group_table_body.after('<tfoot><tr><td colspan="' + columns_num + '">' + App.clean(total_amount_string) + '</td></tr></tfoot>');
          } // if
        }); // each
      },
      'data' : {
        'companies' : {$companies|json nofilter},
        'users' : {$users|json nofilter},
        'payment_status_filter' : {$payment_statuses|map nofilter},
        'currencies' : {$currencies|json nofilter}
      }
    });
  });
</script>