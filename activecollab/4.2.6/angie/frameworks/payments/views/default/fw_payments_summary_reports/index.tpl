{title}Payments Summary{/title}
{add_bread_crumb}Payments Summary{/add_bread_crumb}
{use_widget name="filter_criteria" module="reports"}

<div id="payments_reports" class="filter_criteria">
  <form action="{assemble route=payments_summary_reports_run}" method="get" class="expanded">
  
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

    wrapper.filterCriteria({
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=payments_summary_reports_export}', response, data, links);
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
          'label' : App.lang('For Year'), 
          'choices' : {
            'any' : App.lang('Any Year'), 
            'last_year' : App.lang('Last Year'), 
            'this_year' : App.lang('This Year'), 
            'selected_date' : {
              'label' : App.lang('Selected Year ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectYear'],
              'get_name' : function(c) {
                return 'year';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['year'] : null;
              }
            }, 
            'selected_range' : {
              'label' : App.lang('Selected Year Range ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectYearRange'],
              'get_name' : function(c) {
                return 'year';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? [f['year_from'], f['year_to']] : null;
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
        }
      }, 
      'filters' : {$saved_filters|map nofilter},
      'new_filter_url' : '{assemble route=payments_summary_reports_add}',
      'can_add_filter' : true,
      'on_show_results' : function(response, data, form_data) {
        var results_wrapper = $(this);
        
        // Settings that affect the way results are displayed
        var group_by = 'dont';
       
        // Display results summarized by user
        var columns = [ App.lang('Month'), App.lang('Amount') ];
         
          /**
           * Render payment amount
           *
           * @param Object record
           * @return string
           */
          var render_currency_rows = function(records) {
            var currency_rows;
            
            if(records) {
            	App.each(records, function(currency_id, amount) {
              	currency_rows += '<td class="currency">' + App.numberFormat(amount) +'</td>';
            	});//each
            	
            	return currency_rows;
        	  }//if 
          }; // render_amount

					//render records
          App.each(response, function(year, rows) {
          	if(rows) { 
              var group_label = year;
              var group_wrapper = $('<div class="payments_summary_report_result_group_wrapper">' +
                '<h2>' + group_label + '</h2>' + 
                '<div class="payments_summary_report_result_group_inner_wrapper"></div>' + 
              '</div>').appendTo(results_wrapper);

              var group_table = $('<table class="common records_list" cellspacing="0">' + 
                '<thead></thead>' +
                '<tbody></tbody>' + 
              '</table>').appendTo(group_wrapper.find('div.payments_summary_report_result_group_inner_wrapper'));

              var group_table_head = group_table.find('thead');
              var group_table_body = group_table.find('tbody');

              var head_row = $('<tr></tr>');
              head_row.append('<th class="date left">' + App.lang('Month') + '</th>');

              App.each(data['currencies'], function(currency_id, currency) {
              	head_row.append('<th class="currency ' + currency['code'] +'">'+ currency['code'] +'</th>');
              });//each

              group_table_head.append(head_row);
  
              var total_amount = {};
             
							App.each(rows, function(month_name, records) {
               
                var record_type = 'month_payments';
                
                // Open row and set properties
                var row = '<tr class="' + record_type + '">';

                row += '<td class="date left">' + month_name + '</td>';
                
                row += render_currency_rows(records);
               
                group_table_body.append(row + '</tr>');

								//calculate total per year
                App.each(records, function(currency_id, amount) {
                	if(typeof(total_amount[currency_id]) == 'undefined') {
  									total_amount[currency_id] = 0;
                  } // if  
  								total_amount[currency_id] += amount;
              	});//each

              }); // each

							var total_payment_by_currency = [];

							var tfoot = $('<tfoot><tr></tr></tfoot>');
							tfoot.append('<td class="total">' + App.lang('Total') +'</td>');
							
							App.each(total_amount, function(currency_id, total) {
								tfoot.append('<td class="total_amount">' + App.numberFormat(total) +'</td>');
              }); // for
							
							group_table_body.after(tfoot);
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