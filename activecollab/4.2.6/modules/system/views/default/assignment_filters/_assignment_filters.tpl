{use_widget name="ui_date_picker" module="environment"}
{use_widget name="filter_criteria" module="reports"}

<div id="assignment_filters" class="filter_criteria">
  <form action="{assemble route=assignment_filters_run}" method="get" class="expanded">
  
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
  $('#assignment_filters').each(function() {

    var group_by_choices = {
      'dont' : App.lang("Don't Group"),
      'assignee' : App.lang('Assignee'),
      'project' : App.lang('Project'),
      'project_client' : App.lang('Project Client'),
      'milestone' : App.lang('Milestone'),
      'category' : App.lang('Category'),
      'label' : App.lang('Label'),
      'created_on' : App.lang('Creation Date'),
      'due_on' : App.lang('Due Date'),
      'completed_on' : App.lang('Completion Date')
    };

    var additional_field_choices = {
      'none' : App.lang('None'),
      'assignee' : App.lang('Assignee'),
      'project' : App.lang('Project'),
      'category' : App.lang('Category'),
      'milestone' : App.lang('Milestone'),
      'created_on' : App.lang('Created On'),
      'age' : App.lang('Age'),
      'created_by' : App.lang('Created By'),
      'due_on' : App.lang('Due On'),
      'completed_on' : App.lang('Completed On')
    };

    {if AngieApplication::isModuleLoaded('tracking')}
      additional_field_choices['estimated_time'] = App.lang('Estimated Time');
      additional_field_choices['tracked_time'] = App.lang('Tracked Time');
    {/if}

  {if AngieApplication::isModuleLoaded('tasks')}
    {foreach CustomFields::getEnabledCustomFieldsByType('Task') as $field_name => $details}
      group_by_choices[{$field_name|json nofilter}] = App.lang('Custom Field: :name', {
        'name' : {$details.label|json nofilter}
      });

      additional_field_choices[{$field_name|json nofilter}] = App.lang('Custom Field: :name', {
        'name' : {$details.label|json nofilter}
      });
    {/foreach}
  {/if}

    /**
     * Prepare created on age picker
     *
     * @param string label
     * @param string field
     * @returns Object
     */
    var prepare_age_picker = function(field, label) {
      return {
        'label' : label,
        'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDateAge'],
        'get_name' : function() {
          return 'created_age';
        },
        'get_value' : function(f) {
          return typeof(f) =='object' && f ? f['created_age'] : null;
        }
      };
    };

    $(this).filterCriteria({
      'pre_select_filter_id' : {if $pre_select_filter instanceof AssignmentFilter}{$pre_select_filter->getId()|json nofilter}{else}null{/if},
      'filter_type' : 'Assignments',    
      'options' : {
        'is_private' : {
          'label' : App.lang('Private Filter'),
          'description' : App.lang('When saved, private filters are available only to users who created them')
        },
        'include_all_projects' : {
          'label' : App.lang('Include All Projects'),
          'description' : App.lang('Check if you want this filter to go through all projects when it is executed by an administrator or a project manager, not just through projects that they are assigned to')
        },
      {if AngieApplication::isModuleLoaded('tracking')}
        'include_tracking_data' : {
          'label' : App.lang('Include Time'),
          'description' : App.lang('Load tracked and estimated time, for additional columns and CSV export'),
          'onchange' : function(wrapper) {
            var additional_field_1 = wrapper.find('tr.report_select.criterion_additional_column_1 select');
            var additional_field_2 = wrapper.find('tr.report_select.criterion_additional_column_2 select');

            var additional_field_1_value = additional_field_1.val();
            var additional_field_2_value = additional_field_2.val();

            if(this.checked) {
              if(additional_field_1_value == 'none' && additional_field_2_value == 'none') {
                additional_field_1.val('estimated_time');
                additional_field_2.val('tracked_time');
              } // if
            } else {
              if(additional_field_1_value == 'estimated_time' || additional_field_1_value == 'tracked_time') {
                additional_field_1.val('none');
              } // if

              if(additional_field_2_value == 'estimated_time' || additional_field_2_value == 'tracked_time') {
                additional_field_2.val('none');
              } // if
            } // if
          },
          'selected' : false
        },
      {/if}
        'include_subtasks' : {
          'label' : App.lang('Include Subtasks'), 
          'selected' : true
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
        'label_filter' : {
          'label' : App.lang('Label'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'is_not_set' : App.lang('No Label'), 
            'selected' : {
              'label' : App.lang('Selected ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectLabels'],
              'get_name' : function(c) {
                return 'label_names';
              },
              'get_value' : function(f, c) {
                return typeof(f) =='object' && f ? f['label_names'] : null;
              }
            }
          }
        },
        'category_filter' : {
          'label' : App.lang('Category'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'is_not_set' : App.lang('Not Categorized'), 
            'selected' : {
              'label' : App.lang('Selected ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectCategories'],
              'get_name' : function(c) {
                return 'category_names';
              },
              'get_value' : function(f, c) {
                return typeof(f) =='object' && f ? f['category_names'] : null;
              }
            }
          }
        },
        'milestone_filter' : {
          'label' : App.lang('Milestone'), 
          'choices' : {
            'any' : App.lang('Any'), 
            'is_not_set' : App.lang('No Milestone'), 
            'selected' : {
              'label' : App.lang('Selected ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectMilestones'],
              'get_name' : function(c) {
                return 'milestone_names';
              },
              'get_value' : function(f, c) {
                return typeof(f) =='object' && f ? f['milestone_names'] : null;
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
            'age_is' : prepare_age_picker('created_on', App.lang('Age Is Equal To ...')),
            'age_is_more_than' : prepare_age_picker('created_on', App.lang('Age Is More Than ...')),
            'age_is_less_than' : prepare_age_picker('created_on', App.lang('Age Is Less Than ...')),
            'selected_date' : {
              'label' : App.lang('Select Date ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'created_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['created_on'] : null;
              }
            },
            'before_selected_date' : {
              'label' : App.lang('Before Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'created_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['created_on'] : null;
              }
            },
            'after_selected_date' : {
              'label' : App.lang('After Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'created_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['created_on'] : null;
              }
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
            'late_or_today' : App.lang('Late or Today'),
            'today' : App.lang('Today'), 
            'tomorrow' : App.lang('Tomorrow'), 
            'this_week' : App.lang('This Week'), 
            'next_week' : App.lang('Next Week'), 
            'this_month' : App.lang('This Month'), 
            'next_month' : App.lang('Next Month'), 
            'selected_date' : {
              'label' : App.lang('Select Date ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'due_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['due_on'] : null;
              }
            },
            'before_selected_date' : {
              'label' : App.lang('Before Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'due_on';
              },
              'get_value' : function(f) {
                console.log(f);

                return typeof(f) =='object' && f ? f['due_on'] : null;
              }
            },
            'after_selected_date' : {
              'label' : App.lang('After Selected Date ...'),
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
            'is_not_set' : App.lang('Open (not Completed)'),
            'is_set' : App.lang('Completed at Any Time'),
            'any' : App.lang('Open and Completed'),
            'last_month' : App.lang('Completed Last Month'), 
            'last_week' : App.lang('Completed Last Week'), 
            'yesterday' : App.lang('Completed Yesterday'), 
            'today' : App.lang('Completed Today'), 
            'this_week' : App.lang('Completed This Week'), 
            'this_month' : App.lang('Completed This Month'), 
            'selected_date' : {
              'label' : App.lang('Completed on a Selected Date ...'), 
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'completed_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['completed_on'] : null;
              }
            },
            'before_selected_date' : {
              'label' : App.lang('Completed Before a Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'completed_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['completed_on'] : null;
              }
            },
            'after_selected_date' : {
              'label' : App.lang('Completed After a Selected Date ...'),
              'prepare' : App['Wireframe']['Utils']['dataFilters']['prepareSelectDate'],
              'get_name' : function() {
                return 'completed_on';
              },
              'get_value' : function(f) {
                return typeof(f) =='object' && f ? f['completed_on'] : null;
              }
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
          'choices' : group_by_choices
        },
        'additional_column_1' : {
          'label' : App.lang('Additional Column #1'),
          'choices' : additional_field_choices
        },
        'additional_column_2' : {
          'label' : App.lang('Additional Column #2'),
          'choices' : additional_field_choices
        } 
      }, 
      'filters' : {$assignment_filters|map nofilter},
      'new_filter_url' : {$new_filter_url|json nofilter},
      'can_add_filter' : {if $new_filter_url}true{else}false{/if},
      'on_result_links' : function(response, data, links) {
        App.Wireframe.Utils.reportRegisterExportLinks('{assemble route=assignment_filters_export}', response, data, links);
      },
      'on_show_results' : function(response, data, form_data) {
        if(jQuery.isArray(response)) {
          var map = new App.Map(response);
        } else {
          var map = new App.Map();
        } // if

        var results_wrapper = $(this);
        
        // Settings that affect the way results are displayed
        var group_by = 'dont';
        var additional_columns = {
          'additional_column_1' : 'none', 'additional_column_2' : 'none'
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
         * Return label pill
         * 
         * @param Number label_id
         * @return string
         */
        var render_label = function(label_id) {
          return label_id && typeof(data['labels'][label_id]) == 'object' ? App.Wireframe.Utils.renderLabel(data['labels'][label_id]) : '';
        }; // render_label
        
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
                var priority_text = App.lang('Lowest');
                var priority_class = 'not_important';
                break;
              case -1:
                var priority_text = App.lang('Low');
                var priority_class = 'not_important';
                break;
              case 1:
                var priority_text = App.lang('High');
                var priority_class = 'important';
                break;
              case 2:
                var priority_text = App.lang('Highest');
                var priority_class = 'important';
                break;
            } // switch
            
            return '<span class="pill priority ' + priority_class + '">' + priority_text + '</span>';
          } else {
            return '';
          } // if
        }; // render_priority
        
        /**
         * Return assignment URL
         * 
         * @param Object assignment
         * @param String sufix
         * @return string
         */
        var assignment_url = function(assignment, sufix) {
          var project_slug = data['project_slugs'] && typeof(data['project_slugs'][assignment['project_id']]) == 'string' ? data['project_slugs'][assignment['project_id']] : null;
          
          if(project_slug) {
            if(assignment['type'] == 'Task') {
              return data['task_url'].replace('--PROJECT_SLUG--', project_slug).replace('--TASK_ID--', assignment['task_id']);
            } else {
              return data['todo_url'].replace('--PROJECT_SLUG--', project_slug).replace('--TODO_LIST_ID--', assignment['id']);
            };
          } else {
            return '#';
          } // if
        }; // assignment_url
        
        /**
         * Return subtask URL
         * 
         * @param Object assignment
         * @param Object subtask
         * @param String sufix
         * @return string
         */
        var subtask_url = function(assignment, subtask, sufix) {
          var project_slug = data['project_slugs'] && typeof(data['project_slugs'][assignment['project_id']]) == 'string' ? data['project_slugs'][assignment['project_id']] : null;
          
          if(project_slug) {
            if(assignment['type'] == 'Task') {
              return data['task_subtask_url'].replace('--PROJECT_SLUG--', project_slug).replace('--TASK_ID--', assignment['task_id']).replace('--SUBTASK_ID--', subtask['id']);
            } else {
              return data['todo_subtask_url'].replace('--PROJECT_SLUG--', project_slug).replace('--TODO_LIST_ID--', assignment['id']).replace('--SUBTASK_ID--', subtask['id']);
            };
          } else {
            return '#';
          } // if
        }; // subtask_url

        /**
         * Render estimated time string
         *
         * @param float value
         * @param integer job_type_id
         * @param float tracked_time
         * @return string
         */
        var estimated_time = function(value, job_type_id, tracked_time) {
          var job_type = null;

          if(job_type_id && typeof(data['job_types']) == 'object' && data['job_types']) {
            if(typeof(data['job_types'][job_type_id]) != 'undefined' && data['job_types'][job_type_id]) {
              job_type = data['job_types'][job_type_id];
            } // if
          } // if

          if(job_type) {
            var text = App.lang(':hours of :job', {
              'hours' : App.hoursFormat(value),
              'job' : job_type
            });
          } else {
            var text = App.hoursFormat(value);
          } // if

          if(value && tracked_time > value) {
            return '<span style="color: red; font-weight: bold;">' + text + '</span>';
          } else {
            return text;
          } // if
        }; // estimated_time

        App.each(map, function(group_key, group_data) {
          if(typeof(group_data['assignments']) == 'object') {
            var group_label = typeof(group_data['label']) == 'string' ? group_data['label'] : '--';

            var group_wrapper = $('<div class="assignment_filter_result_group_wrapper">' +
              '<h2>' + App.clean(group_label) + '</h2>' +
              '<div class="assignment_filter_result_group_inner_wrapper"></div>' +
              '</div>').appendTo(results_wrapper);

            var group_table = $('<table class="common" cellspacing="0">' +
              '<tbody></tbody>' +
              '</table>').appendTo(group_wrapper.find('div.assignment_filter_result_group_inner_wrapper'));

            var group_table_body = group_table.find('tbody');

            App.each(group_data['assignments'], function(assignment_id, assignment_data) {
              var assignment_url_base = assignment_url(assignment_data);
              var classes = "assignment";

              if(assignment_data['type'] == 'Task') {
                var assignment_type = 'task';
              } else {
                var assignment_type = 'todo_list';
              } // if
              classes += ' ' + assignment_type;

              if (assignment_data['completed_on']) {
                classes += ' completed';
              } // if

              // Open row and prepare label and priority
              var row = '<tr class="' + classes + '" assignment_id="' + assignment_id + '"><td class="labels">';

              row += render_priority(assignment_data['priority']);
              row += render_label(assignment_data['label_id']);

              // Name and link
              row += '</td><td class="name">';

              // Type
              if(assignment_type == 'task') {
                row += '<span class="object_type object_type_task">' + App.lang('Task') + '</span>';
              } else {
                row += '<span class="object_type object_type_todo_list">' + App.lang('Todo') + '</span>';
              } // if

              row += '<a href="' + assignment_url_base + '" class="assignment_name quick_view_item">' + (assignment_type == 'task' ? '#' + assignment_data['task_id'] + ': ' : '') + App.clean(assignment_data['name']) + '</a></td>';

              for(var additional_column in additional_columns) {
                if(additional_columns[additional_column] && additional_columns[additional_column] != 'none') {
                  row += '<td class="additional_column ' + additional_column + '">';

                  switch(additional_columns[additional_column]) {
                    case 'assignee':
                      if(assignment_type == 'task') {
                        if(assignment_data['assignee_id'] && typeof(assignment_data['assignee']) == 'string') {
                          row += App.clean(assignment_data['assignee']);
                        } else {
                          row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                        } // if
                      } // if

                      break;

                    case 'project':
                      if(assignment_data['project_id'] && typeof(assignment_data['project']) == 'string') {
                        row += App.clean(assignment_data['project']);
                      } else {
                        row += '<span class="empty">' + App.lang('Unknown') + '</span>';
                      } // if

                      break;

                    case 'milestone':
                      if(assignment_data['milestone_id'] && typeof(assignment_data['milestone']) == 'string') {
                        row += App.clean(assignment_data['milestone']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if

                      break;

                    case 'category':
                      if(assignment_data['category_id'] && typeof(assignment_data['category']) == 'string') {
                        row += App.clean(assignment_data['category']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if

                      break;

                    case 'created_on':
                      if(assignment_data['created_on'] && typeof(assignment_data['created_on']) == 'object') {
                        row += App.clean(assignment_data['created_on']['formatted_date_gmt']);
                      } // if

                      break;

                    case 'age':
                      if(typeof(assignment_data['age']) != 'undefined') {
                        if(assignment_data['age'] == 1) {
                          row += App.lang('One Day');
                        } else {
                          row += App.lang(':num Days', {
                            'num' : assignment_data['age']
                          });
                        } // if
                      } // if

                      break;
                    case 'created_by':
                      if(typeof(assignment_data['created_by']) == 'string') {
                        row += App.clean(assignment_data['created_by']);
                      } // if

                      break;
                    case 'due_on':
                      if(assignment_type == 'task') {
                        if(assignment_data['due_on'] && typeof(assignment_data['due_on']) == 'object') {
                          row += App.clean(assignment_data['due_on']['formatted_gmt']);
                        } else {
                          row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                        } // if
                      } // if

                      break;
                    case 'completed_on':
                      if(assignment_type == 'task') {
                        if(assignment_data['completed_on'] && typeof(assignment_data['completed_on']) == 'object') {
                          row += App.clean(assignment_data['completed_on']['formatted_date_gmt']);
                        } else {
                          row += '<span class="empty">' + App.lang('Open') + '</span>';
                        } // if
                      } // if

                      break;
                    case 'estimated_time':
                      if(typeof(assignment_data['estimated_time']) != 'undefined' && assignment_data['estimated_time'] > 0) {
                        var job_type_id = typeof(assignment_data['estimated_job_type_id']) != 'undefined' ? assignment_data['estimated_job_type_id'] : null;
                        var tracked_time = typeof(assignment_data['tracked_time']) != 'undefined' ? assignment_data['tracked_time'] : 0;

                        row += estimated_time(assignment_data['estimated_time'], job_type_id, tracked_time);
                      } else {
                        row += '<span class="empty">' + App.lang('Empty') + '</span>';
                      } // if

                      break;
                    case 'tracked_time':
                      if(assignment_data['tracked_time'] && typeof(assignment_data['tracked_time']) == 'number') {
                        row += App.hoursFormat(assignment_data['tracked_time']);
                      } else {
                        row += '<span class="empty">' + App.lang('Empty') + '</span>';
                      } // if

                      break;
                    case 'custom_field_1':
                      if(assignment_data['custom_field_1'] && typeof(assignment_data['custom_field_1']) == 'string') {
                        row += App.clean(assignment_data['custom_field_1']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if
                    
                      break;
                    case 'custom_field_2':
                      if(assignment_data['custom_field_2'] && typeof(assignment_data['custom_field_2']) == 'string') {
                        row += App.clean(assignment_data['custom_field_2']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if

                      break;
                    case 'custom_field_3':
                      if(assignment_data['custom_field_3'] && typeof(assignment_data['custom_field_3']) == 'string') {
                        row += App.clean(assignment_data['custom_field_3']);
                      } else {
                        row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                      } // if

                      break;
                  } // switch

                  row += '</td>';
                } // if
              } // for

              // Options
              row += '</tr>';

              group_table_body.append(row);

              // Subtasks
              if(typeof(assignment_data['subtasks']) != 'undefined' && jQuery.isArray(assignment_data['subtasks'])) {
                App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
                  var subtask_url_base = subtask_url(assignment_data, subtask_data);
                  var classes = 'assignment subtask';

                  if(subtask_data['completed_on']) {
                    classes += ' completed';
                  } // if

                  var row = '<tr class="' + classes + '" assignment_id="' + assignment_id + '" subtask_id="subtask_id"><td class="labels">';

                  row += render_priority(subtask_data['priority']);
                  row += render_label(subtask_data['label_id']);

                  row += '<td class="name"><span class="object_type object_type_subtask">' + App.lang('Subtask') + '</span> <a href="' + subtask_url_base + '" class="quick_view_item assignment_name">' + App.clean(subtask_data['body']) + '</a></td>';

                  for(var additional_column in additional_columns) {
                    if(additional_columns[additional_column] && additional_columns[additional_column] != 'none') {
                      row += '<td class="additional_column ' + additional_column + '">';

                      switch(additional_columns[additional_column]) {
                        case 'assignee':
                          if(subtask_data['assignee_id'] && typeof(subtask_data['assignee']) == 'string') {
                            row += App.clean(subtask_data['assignee']);
                          } else {
                            row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                          } // if

                          break;
                        case 'created_on':
                          if(subtask_data['created_on'] && typeof(subtask_data['created_on']) == 'object') {
                            row += App.clean(subtask_data['created_on']['formatted_date']);
                          } // if

                          break;
                        case 'age':
                          if(typeof(subtask_data['age']) != 'undefined') {
                            if(subtask_data['age'] == 1) {
                              row += App.lang('One Day');
                            } else {
                              row += App.lang(':num Days', {
                                'num' : subtask_data['age']
                              });
                            } // if
                          } // if

                          break;
                        case 'created_by':
                          if(typeof(subtask_data['created_by']) == 'string') {
                            row += App.clean(subtask_data['created_by']);
                          } // if

                          break;
                        case 'due_on':
                          if(subtask_data['due_on'] && typeof(subtask_data['due_on']) == 'object') {
                            row += App.clean(subtask_data['due_on']['formatted_gmt']);
                          } else {
                            row += '<span class="empty">' + App.lang('Not Set') + '</span>';
                          } // if

                          break;
                        case 'completed_on':
                          if(subtask_data['completed_on'] && typeof(subtask_data['completed_on']) == 'object') {
                            row += App.clean(subtask_data['completed_on']['formatted_date']);
                          } else {
                            row += '<span class="empty">' + App.lang('Open') + '</span>';
                          } // if

                          break;
                          
                        case 'custom_field_1':
                        case 'custom_field_2':
                        case 'custom_field_3':
                          row += '<span class="empty" title="' + App.lang('Custom Fields are not Available for Subtasks') + '" style="cursor: help">' + App.lang('Not Available') + '</span>';
                          break;
                      } // switch

                      row += '</td>';
                    } // if
                  } // for

                  row += '</tr>';

                  group_table_body.append(row);
                });
              } // if
            });
          } // if
        });
      }, 
      'data' : {
      {if AngieApplication::isModuleLoaded('todo')} 
        'todo_url' : "{assemble route=project_todo_list project_slug='--PROJECT_SLUG--' todo_list_id='--TODO_LIST_ID--'}", 
        'todo_subtask_url' : "{assemble route=project_todo_list_subtask project_slug='--PROJECT_SLUG--' todo_list_id='--TODO_LIST_ID--' subtask_id='--SUBTASK_ID--'}", 
      {/if}
      {if AngieApplication::isModuleLoaded('tasks')}
        'task_url' : "{assemble route=project_task project_slug='--PROJECT_SLUG--' task_id='--TASK_ID--'}", 
        'task_subtask_url' : "{assemble route=project_task_subtask project_slug='--PROJECT_SLUG--' task_id='--TASK_ID--' subtask_id='--SUBTASK_ID--'}", 
      {/if}
        'users' : {$users|map nofilter},
        'companies' : {$companies|map nofilter},
        'projects' : {$projects|map nofilter},
        'milestones' : {$milestones|map nofilter},
        'active_projects' : {$active_projects|map nofilter},
        'project_slugs' : {$project_slugs|json nofilter},
        'project_categories' : {$project_categories|map nofilter},
        'categories' : {$categories|map nofilter},
        'labels' : {$labels|json nofilter},
        'job_types' : {$job_types|json nofilter}
      }
    });
  });
</script>