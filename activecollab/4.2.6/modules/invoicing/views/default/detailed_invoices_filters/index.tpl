{title}Invoices{/title}
{add_bread_crumb}Filter Invoices{/add_bread_crumb}

{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="detailed_invoices_filter" class="filter_criteria">
  <form action="{assemble route=detailed_invoices_filters_run}" method="get" class="expanded">
  
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
  $('#detailed_invoices_filter').each(function() {
    var wrapper = $(this);
    
    wrapper.filterCriteria({
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=detailed_invoices_filters_export}', response, data, links);
      },
      'options' : {
        'include_credit_invoices' : {
          'label' : App.lang('Include Credit Invoices'),
          'description' : App.lang('Include invoices with either a zero or a negative amount in your report.'),
          'selected' : false
        }
      },
      'criterions' : {
        'status_filter' : {
          'label' : App.lang('Status'),
          'choices' : {
            'all_except_canceled' : App.lang('Any (Except Canceled)'),
            'draft' : App.lang('Draft'),
            'issued' : App.lang('Issued'),
            'overdue' : App.lang('Overdue'),
            'paid' : App.lang('Paid'),
            'canceled' : App.lang('Canceled')
          }
        },
        'client_filter' : {
          'label' : App.lang('Client'),
          'choices' : {
            'any' : App.lang('Any'),
            'selected' : {
              'label' : App.lang('Select Client ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'client_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['client_id'] : null;
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
        'issued_by_filter' : {
          'label' : App.lang('Issued By'),
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'logged_user' : App.lang('Person Accessing This Report'), 
            'company' : {
              'label' : App.lang('Members of a Company ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'issued_by_company_member_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['issued_by_company_member_id'] : null;
              }
            },
            'selected' : {
              'label' : App.lang('Selected Users ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'issued_by_user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['issued_by_user_ids'] : null;
              }
            }
          }
        }, 
        'issued_on_filter' : {
          'label' : App.lang('Issued On'),
          'choices' : {
            'any' : App.lang('Any Day'), 
            'last_month' : App.lang('Last Month'), 
            'last_week' : App.lang('Last Week'), 
            'yesterday' : App.lang('Yesterday'), 
            'today' : App.lang('Today'), 
            'this_week' : App.lang('This Week'),
            'this_month' : App.lang('This Month'), 
            'selected_date' : {
              'label' : App.lang('Selected Date ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'issued_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['issued_on'] : null;
              }
            }, 
            'selected_range' : {
              'label' : App.lang('Selected Date Range ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
              'get_name' : function() {
                return 'issued';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? [f['issued_from'], f['issued_to']] : null;
              }
            }
          }
        },
        'due_on_filter' : {
          'label' : App.lang('Due On'),
          'choices' : {
            'any' : App.lang('Any Day'),
            'late' : App.lang('Late'), 
            'last_month' : App.lang('Last Month'),
            'last_week' : App.lang('Last Week'),
            'yesterday' : App.lang('Yesterday'),
            'today' : App.lang('Today'),
            'this_week' : App.lang('This Week'),
            'next_week' : App.lang('Next Week'),
            'this_month' : App.lang('This Month'),
            'next_month' : App.lang('Next Month'),
            'selected_date' : {
              'label' : App.lang('Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'due_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['due_on'] : null;
              }
            },
            'selected_range' : {
              'label' : App.lang('Selected Date Range ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
              'get_name' : function() {
                return 'due';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? [f['due_from'], f['due_to']] : null;
              }
            }
          }
        },
        'closed_on_filter' : {
          'label' : App.lang('Closed On'),
          'choices' : {
            'any' : App.lang('Any Day'),
            'last_month' : App.lang('Last Month'),
            'last_week' : App.lang('Last Week'),
            'yesterday' : App.lang('Yesterday'),
            'today' : App.lang('Today'),
            'this_week' : App.lang('This Week'),
            'this_month' : App.lang('This Month'),
            'selected_date' : {
              'label' : App.lang('Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'closed_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['closed_on'] : null;
              }
            },
            'selected_range' : {
              'label' : App.lang('Selected Date Range ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
              'get_name' : function() {
                return 'closed';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? [f['closed_from'], f['closed_to']] : null;
              }
            }
          }
        },
        'group_by' : {
          'label' : App.lang('Group By'), 
          'choices' : {
            'all' : App.lang("Don't Group"),
            'status' : App.lang('Status'),
            'project' : App.lang('Project'),
            'client' : App.lang('Client'),
            'issued_on' : App.lang('Issue Date'),
            'due_on' : App.lang('Due Date'),
            'closed_on' : App.lang('Close Date')
          }
        }
      }, 
      'filters' : {$saved_filters|map nofilter},
      'new_filter_url' : '{assemble route=detailed_invoices_filters_add}',
      'can_add_filter' : true,
      'on_show_results' : function(response, data, form_data) {
        var results_wrapper = $(this);
        var group_by = 'dont'; // Settings that affect the way results are displayed
        var include_credit_invoices = false;

        if(jQuery.isArray(form_data)) {
          App.each(form_data, function(form_data, field) {
            if(field['name'] == 'filter[group_by]') {
              group_by = field['value'];
            } // if

            if(field['name'] == 'filter[include_credit_invoices]') {
              include_credit_invoices = field['value'] == '1';
            } // if
          });
        } // if

        /**
         * Render invoice number
         *
         * @param Object invoice
         * @return string
         */
        var render_invoice_number = function(invoice) {
          if(invoice['status'] > 0) {
            return '<a href="' + App.clean(invoice['url']) + '">#' + App.clean(invoice['name']) + '</a>';
          } else {
            return '<a href="' + App.clean(invoice['url']) + '">' + App.clean(invoice['name']) + '</a>';
          } // if
        }; // render_date

        /**
         * Render invoice status
         *
         * @param Object invoice
         * @return string
         */
        var render_status = function(invoice) {
          switch(invoice['status']) {
            case 0:
              return App.lang('Draft');
            case 1:
              return App.lang('Issued');
            case 2:
              return App.lang('Paid');
            case 3:
              return App.lang('Canceled');
            default:
              return App.lang('Unknown');
          } // if
        }; // render_status

        /**
         * Render invoice client
         *
         * @param Object invoice
         * @return string
         */
        var render_client = function(invoice) {
          if(typeof(invoice['client']) == 'object' && invoice['client'] && invoice['client']['id']) {
            return '<a href="' + App.clean(invoice['client']['url']) + '">' + App.clean(invoice['client']['name']) + '</a>';
          } else {
            return App.lang('N/A');
          } // if
        }; // render_user

        /**
         * Render project name
         *
         * @param Object invoice
         * @return string
         */
        var render_project_name = function(invoice) {
          if(typeof(invoice['project']) == 'object' && invoice['project'] && invoice['project']['id']) {
            return '<a href="' + App.clean(invoice['project']['url']) + '">' + App.clean(invoice['project']['name']) + '</a>';
          } else {
            return App.lang('Not Set');
          } // if
        }; // render_project_name

        /**
         * Render payment amount
         *
         * @param Object invoice
         * @return string
         */
        var render_amount = function(invoice, field) {
          if(typeof(invoice[field]) == 'number' && invoice[field]) {
            var currency_id = invoice['currency_id'];
            var currency = currency_id && typeof(data['currencies'][currency_id]) == 'object' && data['currencies'][currency_id] ? data['currencies'][currency_id] : null;

            return App.moneyFormat(invoice[field], currency, null, true);
          } else {
            return '--';
          } // if
        }; // render_amount

        /**
         * Render invoice date
         *
         * @param Object invoice
         * @return string
         */
        var render_date = function(invoice, field) {
          return typeof(invoice[field]) == 'object' && invoice[field] ? App.clean(invoice[field]['formatted_date_gmt']) : '--';
        }; // render_date

        // Render invoices
        App.each(response, function(group_id, group) {
          if(jQuery.isArray(group['invoices']) && group['invoices'].length) {
            var group_label = typeof(group['label']) == 'string' ? group['label'] : '--';
            var group_wrapper = $('<div class="detailed_invoices_filter_results_group_wrapper">' +
              '<h2>' + App.clean(group_label) + '</h2>' +
              '<div class="detailed_invoices_filter_result_group_inner_wrapper"></div>' +
            '</div>').appendTo(results_wrapper);

            var group_table = $('<table class="common records_list" cellspacing="0">' +
              '<thead></thead>' +
              '<tbody></tbody>' +
            '</table>').appendTo(group_wrapper.find('div.detailed_invoices_filter_result_group_inner_wrapper'));

            var group_table_head = group_table.find('thead');
            var group_table_body = group_table.find('tbody');

            group_table_head.append('<tr>' +
              '<th class="invoice_num">' + App.lang('Invoice #') + '</th>' +
              '<th class="status center">' + App.lang('Status') + '</th>' +
              '<th class="client">' + App.lang('Client') + '</th>' +
              '<th class="project_name">' + App.lang('Project') + '</th>' +
              '<th class="issued_on">' + App.lang('Issued On') + '</th>' +
              '<th class="due_on">' + App.lang('Due On') + '</th>' +
              '<th class="closed_on">' + App.lang('Paid or Canceled On') + '</th>' +
              '<th class="paid_amount right">' + App.lang('Paid Amount') + '</th>' +
              '<th class="balance_due right">' + App.lang('Balance Due') + '</th>' +
              '<th class="total right">' + App.lang('Total') + '</th>' +
            '</tr>');

            // Remove cloumn that we are grouping by (not needed)
            var extra_column = group_table_head.find('th.' + group_by);

            if(extra_column.length) {
              extra_column.remove();
            } // if

            // Lets do our homework and prepare variables for data summary at the bottom of the group
            var columns_num = group_table_head.find('tr th').length;

            App.each(group['invoices'], function(invoice_id, invoice) {
              var row = '<tr class="invoice" invoice_id="' + invoice_id + '" currency_id="' + invoice['currency_id'] + '">';

              row += '<td class="invoice quick_view_item">' + render_invoice_number(invoice) + '</td>';

              if(group_by != 'status') {
                row += '<td class="status center">' + render_status(invoice) + '</td>';
              } // if

              if(group_by != 'client') {
                row += '<td class="client quick_view_item">' + render_client(invoice) + '</td>';
              } // if

              if(group_by != 'project') {
                row += '<td class="project_name quick_view_item">' + render_project_name(invoice) + '</td>';
              } // if

              if(group_by != 'issued_on') {
                row += '<td class="issued_on">' + render_date(invoice, 'issued_on') + '</td>';
              } // if

              if(group_by != 'due_on') {
                row += '<td class="due_on">' + render_date(invoice, 'due_on') + '</td>';
              } // if

              if(group_by != 'closed_on') {
                row += '<td class="closed_on">' + render_date(invoice, 'closed_on') + '</td>';
              } // if

              row += '<td class="paid_amount right">' + render_amount(invoice, 'paid_amount') + '</td>';
              row += '<td class="balance_due right">' + render_amount(invoice, 'balance_due') + '</td>';
              row += '<td class="total right">' + render_amount(invoice, 'total') + '</td>';

              group_table_body.append(row + '</tr>');
            }); // each

            var get_formatted_totals = function(from) {
              var totals = [];

              App.each(from, function(currency_id, total) {
                var currency = typeof(data['currencies'][currency_id]) == 'object' ? data['currencies'][currency_id] : null;
                totals.push(App.moneyFormat(total, currency, null, currency ? true : false));
              });

              return totals.join(', ');
            }; // get_formatted_totals

            group_table_body.after('<tfoot><tr><td colspan="' + columns_num + '" class="right"><span>' + App.lang('Amount Due') + ':</span> ' + App.clean(get_formatted_totals(group['total_due'])) + ' &middot; <span>' + App.lang('Total Invoiced') + ':</span> ' + App.clean(get_formatted_totals(group['total'])) + '</td></tr></tfoot>');
          } // if
        }); // each
      },
      'data' : {
        'users' : {$users|map nofilter},
        'companies' : {$companies|map nofilter},
        'projects' : {$projects|map nofilter},
        'active_projects' : {$active_projects|map nofilter},
        'project_categories' : {$project_categories|map nofilter},
        'currencies' : {$currencies|json nofilter}
      }
    });
  });
</script>