{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="milestone_filters" class="filter_criteria">
  <form action="{assemble route=milestone_filters_run}" method="get" class="expanded">
  
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
  $('#milestone_filters').each(function() {

    /**
     * Prepare date picker
     *
     * @param String submit_as
     * @param String criterion
     * @param Object filter
     * @param Object data
     */
    var prepare_date = function(submit_as, criterion, filter, data) {
      switch(criterion) {
        case 'created_on_filter':
          var name = 'created_on'; break;
        case 'due_on_filter':
          var name = 'due_on'; break;
        case 'completed_on_filter':
          var name = 'completed_on'; break;
      } // switch

      // Value
      if(typeof(filter) == 'object' && filter) {
        switch(criterion) {
          case 'created_on_filter':
            var value = filter['created_on']; break;
          case 'due_on_filter':
            var value = filter['due_on']; break;
          case 'completed_on_filter':
            var value = filter['completed_on']; break;
        } // switch
      } else {
        var value = '';
      } // if
      
      var select = $('<div class="select_date"><input name="' + submit_as + '[' + name + ']" value="' + (typeof(value) == 'object' && value ? value['mysql'] : '') + '" /></div>');
      
      select.find('input').datepicker({
        'dateFormat' : "yy/mm/dd",
        'minDate' : new Date("2000/01/01"),
        'maxDate' : new Date("2050/01/01"),
        'showAnim' : "blind",
        'duration' : 0,
        'changeYear' : true,
        'showOn' : "both",
        'buttonImage' : App.Wireframe.Utils.imageUrl('icons/16x16/calendar.png', 'tracking'),
        'buttonImageOnly' : true,
        'buttonText' : App.lang('Select Date'),
        'firstDay' : App.Config.get('first_week_day'),
        'hideIfNoPrevNext' : true,
        'defaultDate' : typeof(value) == 'object' && value ? new Date(value['mysql']) : new Date()
      });

      $(this).append(select);
    }; // prepare_date

    $(this).filterCriteria({
      'pre_select_filter_id' : {if $pre_select_filter instanceof MilestoneFilter}{$pre_select_filter->getId()|json nofilter}{else}null{/if},
      'filter_type' : 'milestones',    
      'options' : {
        'include_all_projects' : {
          'label' : App.lang('Include All Projects')
        }
      },
      'criterions' : {
        'user_filter' : {
          'label' : App.lang('Assigned To'), 
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'not_assigned' : App.lang('Not Assigned'), 
            'logged_user' : App.lang('Person Using the Filter is Assigned or Responsible'), 
            'logged_user_responsible' : App.lang('Person Using the Filter is Responsible Only'), 
            'company' : {
              'label' : App.lang('Members of a Company are Assigned or Responsible ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['company_id'] : null;
              }
            }, 
            'company_responsible' : {
              'label' : App.lang('Members of a Company are Responsible ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['company_id'] : null;
              }
            }, 
            'selected' : {
              'label' : App.lang('Selected Users are Assigned or Responsible ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['user_ids'] : null;
              }
            }, 
            'selected_responsible' : {
              'label' : App.lang('Selected Users are Responsible ...'), 
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
        'created_by_filter' : {
          'label' : App.lang('Created By'), 
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'anonymous' : App.lang('Anonymous'), 
            'logged_user' : App.lang('Person Using the Filter'), 
            'company' : {
              'label' : App.lang('Members of a Company ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'created_by_company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['created_by_company_id'] : null;
              }
            },
            'selected' : {
              'label' : App.lang('Selected Users ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'created_by_user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['created_by_user_ids'] : null;
              }
            }
          }
        }, 
        'delegated_by_filter' : {
          'label' : App.lang('Delegated By'), 
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'logged_user' : App.lang('Person Using the Filter'), 
            'company' : {
              'label' : App.lang('Members of a Company ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'delegated_by_company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['delegated_by_company_id'] : null;
              }
            },
            'selected' : {
              'label' : App.lang('Selected Users ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'delegated_by_user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['delegated_by_user_ids'] : null;
              }
            }
          }
        },
        'created_on_filter' : {
          'label' : App.lang('Created On'), 
          'choices' : {
            'any' : App.lang('Any Time'), 
            'last_month' : App.lang('Last Month'), 
            'last_week' : App.lang('Last Week'), 
            'yesterday' : App.lang('Yesterday'), 
            'today' : App.lang('Today'), 
            'this_week' : App.lang('This Week'), 
            'this_month' : App.lang('This Month'),  
            'selected_date' : {
              'label' : App.lang('Select Date ...'), 
              'prepare' : prepare_date
            },
            'selected_range' : {
              'label' : App.lang('Select Date Range ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
              'get_name' : function() {
                return 'created';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? [f['created_from'], f['created_to']] : null;
              }
            }
          }
        }, 
        'due_on_filter' : {
          'label' : App.lang('Due On'), 
          'choices' : {
            'any' : App.lang('Any Time'), 
            'is_not_set' : App.lang('Due Date not Set'), 
            'late' : App.lang('Late'), 
            'today' : App.lang('Today'), 
            'tomorrow' : App.lang('Tomorrow'), 
            'this_week' : App.lang('This Week'), 
            'next_week' : App.lang('Next Week'), 
            'this_month' : App.lang('This Month'), 
            'next_month' : App.lang('Next Month'), 
            'selected_date' : {
              'label' : App.lang('Select Date ...'), 
              'prepare' : prepare_date
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
        'completed_by_filter' : {
          'label' : App.lang('Completed By'), 
          'choices' : {
            'anybody' : App.lang('Anybody'), 
            'logged_user' : App.lang('Person Using the Filter'), 
            'company' : {
              'label' : App.lang('Members of a Company ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCompany'],
              'get_name' : function(c) {
                return 'completed_by_company_id';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['completed_by_company_id'] : null;
              }
            },
            'selected' : {
              'label' : App.lang('Selected Users ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectUsers'],
              'get_name' : function(c) {
                return 'completed_by_user_ids';
              },
              'get_value' : function(f, c) {
                return typeof(f) == 'object' && f ? f['completed_by_user_ids'] : null;
              }
            }
          }
        },
        'completed_on_filter' : {
          'label' : App.lang('Completed On'), 
          'choices' : {
            'any' : App.lang('Open and Completed'), 
            'is_not_set' : App.lang('Not Yet Completed'), 
            'is_set' : App.lang('Completed at Any Time'), 
            'last_month' : App.lang('Completed Last Month'), 
            'last_week' : App.lang('Completed Last Week'), 
            'yesterday' : App.lang('Completed Yesterday'), 
            'today' : App.lang('Completed Today'), 
            'this_week' : App.lang('Completed This Week'), 
            'this_month' : App.lang('Completed This Month'), 
            'selected_date' : {
              'label' : App.lang('Completed on a Selected Date ...'), 
              'prepare' : prepare_date
            },
            'selected_range' : {
              'label' : App.lang('Completed in Selected Date Range ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateRange'],
              'get_name' : function() {
                return 'completed';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? [f['completed_from'], f['completed_to']] : null;
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
        'group_by' : {
          'label' : App.lang('Group By'), 
          'choices' : {
            'dont' : App.lang("Don't Group"), 
            'assignee' : App.lang('Assignee'), 
            'project' : App.lang('Project'),  
            'project_client' : App.lang('Project Client'),
            'created_on' : App.lang('Creation Date'), 
            'due_on' : App.lang('Due Date'), 
            'completed_on' : App.lang('Completion Date')
          }
        },
        'additional_column_1' : {
          'label' : App.lang('Additional Column #1'),
          'choices' : {
            'none' : App.lang('None'), 
            'assignee' : App.lang('Assignee'), 
            'project' : App.lang('Project'),
            'created_on' : App.lang('Created On'), 
            'created_by' : App.lang('Created By'), 
            'due_on' : App.lang('Due On'), 
            'completed_on' : App.lang('Completed On') 
          }
        },
        'additional_column_2' : {
          'label' : App.lang('Additional Column #2'),
          'choices' : {
            'none' : App.lang('None'), 
            'assignee' : App.lang('Assignee'), 
            'project' : App.lang('Project'),
            'created_on' : App.lang('Created On'), 
            'created_by' : App.lang('Created By'), 
            'due_on' : App.lang('Due On'), 
            'completed_on' : App.lang('Completed On') 
          }
        } 
      }, 
      'filters' : {$milestone_filters|json nofilter},
      'new_filter_url' : {$new_filter_url|json nofilter},
      'can_add_filter' : {if $new_filter_url}true{else}false{/if},
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=milestone_filters_export}', response, data, links);
      },
      'on_show_results' : function(response, data, form_data) {
        var results_wrapper = $(this);
        
        // Settings that affect the way results are displayed
        var group_by = 'dont';
        var additional_columns = {
          'additional_column_1' : 'none',
          'additional_column_2' : 'none'
        };
        var show_stats = false;
        
        if(jQuery.isArray(form_data)) {
          for(var i in form_data) {
            switch(form_data[i]['name']) {
              case 'filter[group_by]':
                group_by = form_data[i]['value'];
                break;
              case 'filter[additional_column_1]':
                additional_columns['additional_column_1'] = form_data[i]['value'];
                break;
              case 'filter[additional_column_2]':
                additional_columns['additional_column_2'] = form_data[i]['value'];
                break;
              case 'filter[show_stats]':
                show_stats = form_data[i]['value'] ? true : false;
                break;
            } // switch
          } // for
        } // if
        
        /**
         * Return priority pill
         * 
         * @param Number priority_id
         * @return String
         */
        var render_priority = function(priority_id) {
          if(priority_id == -2 || priority_id == -1 || priority_id == 1 || priority_id == 2) {
            switch(priority_id) {
              case -2:
                var priority_text = App.lang('Lowest');  var priority_class = 'not_important'; break;
              case -1:
                var priority_text = App.lang('Low');     var priority_class = 'not_important'; break;
              case 1:
                var priority_text = App.lang('High');    var priority_class = 'important'; break;
              case 2:
                var priority_text = App.lang('Highest'); var priority_class = 'important'; break;
            } // switch
            
            return '<span class="pill priority ' + priority_class + '">' + priority_text + '</span>';
          } else {
            return '';
          } // if
        }; // render_priority
        
        /**
         * Return milestone URL
         * 
         * @param Object milestone
         * @param String suffix
         * @return string
         */
        var milestone_url = function(milestone, suffix) {
          var project_slug = data['project_slugs'] && typeof(data['project_slugs'][milestone['project_id']]) == 'string' ? data['project_slugs'][milestone['project_id']] : null;
          
          if(project_slug) {
            return data['milestone_url'].replace('--PROJECT_SLUG--', project_slug).replace('--MILESTONE_ID--', milestone['task_id']);
          } else {
            return '#';
          } // if
        }; // milestone_url
        
        for(var group_id in response) {
          if(typeof(response[group_id]['milestones']) == 'object') {
            var group_label = typeof(response[group_id]['label']) == 'string' ? response[group_id]['label'] : '--';
            var group_wrapper = $('<div class="milestone_filter_result_group_wrapper">' +
              '<h2>' + App.clean(group_label) + '</h2>' +
              '<div class="milestone_filter_result_group_inner_wrapper"></div>' +
            '</div>').appendTo(results_wrapper);
            
            var group_table = $('<table class="common" cellspacing="0">' + 
              '<tbody></tbody>' + 
            '</table>').appendTo(group_wrapper.find('div.milestone_filter_result_group_inner_wrapper'));
            
            var group_table_body = group_table.find('tbody');
            
            for(var milestone_id in response[group_id]['milestones']) {
              var milestone_url_base = milestone_url(response[group_id]['milestones'][milestone_id]);
              
              // Open row and prepare label and priority
              var row = '<tr class="milestone" milestone_id="' + milestone_id + '"><td class="labels">';
              
              row += render_priority(response[group_id]['milestones'][milestone_id]['priority']);
              
              // Name and link
              row += '</td><td class="name">';
              
              row += '<a href="' + milestone_url_base + '" class="milestone_name">' + App.clean(response[group_id]['milestones'][milestone_id]['name']) + '</a></td>';
              
              for(var additional_column in additional_columns) {
                if(additional_columns[additional_column] && additional_columns[additional_column] != 'none') {
                  row += '<td class="additional_column ' + additional_column + '">';
                  
                  switch(additional_columns[additional_column]) {
                    case 'assignee':
                      if(response[group_id]['milestones'][milestone_id]['assignee_id'] && typeof(response[group_id]['milestones'][milestone_id]['assignee']) == 'string') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['assignee']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if
                      
                      break;
                      
                    case 'project':
                      if(response[group_id]['milestones'][milestone_id]['project_id'] && typeof(response[group_id]['milestones'][milestone_id]['project']) == 'string') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['project']);
                      } else {
                        row += '<span class="empty">' + App.lang('Unknown') + '</span>';
                      } // if
                      
                      break;
                      
                    case 'milestone':
                      if(response[group_id]['milestones'][milestone_id]['milestone_id'] && typeof(response[group_id]['milestones'][milestone_id]['milestone']) == 'string') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['milestone']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if
                      
                      break;
                      
                    case 'category':
                      if(response[group_id]['milestones'][milestone_id]['category_id'] && typeof(response[group_id]['milestones'][milestone_id]['category']) == 'string') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['category']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if
                      
                      break;
                      
                    case 'created_on':
                      if(response[group_id]['milestones'][milestone_id]['created_on'] && typeof(response[group_id]['milestones'][milestone_id]['created_on']) == 'object') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['created_on']['formatted_date_gmt']);
                      } // if
                      
                      break;
                    case 'created_by':
                      if(typeof(response[group_id]['milestones'][milestone_id]['created_by']) == 'string') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['created_by']);
                      } // if
                      
                      break;
                    case 'due_on':
                      if(response[group_id]['milestones'][milestone_id]['due_on'] && typeof(response[group_id]['milestones'][milestone_id]['due_on']) == 'object') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['due_on']['formatted_gmt']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if
                      
                      break;
                    case 'completed_on':
                      if(response[group_id]['milestones'][milestone_id]['completed_on'] && typeof(response[group_id]['milestones'][milestone_id]['completed_on']) == 'object') {
                        row += App.clean(response[group_id]['milestones'][milestone_id]['completed_on']['formatted_date_gmt']);
                      } else {
                        row += '<span class="empty">' + App.lang('Open') + '</span>';
                      } // if
                      
                      break;
                  } // switch
                  
                  row += '</td>';
                } // if
              } // for
              
              // Options
              row += '</tr>';
              
              group_table_body.append(row);
            } // for
          } // if
        } // for
      }, 
      'data' : {
        'milestone_url' : "{assemble route=project_milestone project_slug='--PROJECT_SLUG--' milestone_id='--MILESTONE_ID--'}",
        'users' : {$users|json nofilter},
        'companies' : {$companies|map nofilter},
        'projects' : {$projects|map nofilter},
        'active_projects' : {$active_projects|map nofilter},
        'project_slugs' : {$project_slugs|json nofilter},
        'project_categories' : {$project_categories|json nofilter}
      }
    });
  });
</script>