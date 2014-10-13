{title}Workload{/title}
{add_bread_crumb}Workload{/add_bread_crumb}
{use_widget name="filter_criteria" module="reports"}
{use_widget name="workload_reports" module="system"}
{use_widget name="ui_droppable" module="environment"}

<div id="workload_report" class="filter_criteria">
  <form action="{assemble route=workload_reports_run}" method="get" class="expanded">

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
	$('#workload_report').each(function() {
  	var wrapper = $(this);
  	
  	wrapper.filterCriteria({
	  	'options' : {
	      'include_subtasks' : {
	        'label' : App.lang('Include Subtasks'), 
	        'selected' : true
	      }
	    },
	    'criterions' : {
	      'user_filter' : {
	        'label' : App.lang('Users'), 
	        'choices' : {
	        	'my_company' : App.lang('Members of My Company'),
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
	        'label' : App.lang('Time Span'),
	        'choices' : {
	          'today' : App.lang('Day'),
	          'this_week' : App.lang('Week'),
	          'this_month' : App.lang('Month')
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
	    'new_filter_url' : '{assemble route=workload_reports_add}',
	    'can_add_filter' : true,
	    'on_show_results' : function(response, data, form_data) {
	      var results_wrapper = $(this);
	      
	      var constants = {
			   'item_width': 333,
			   'assignment_height': 27,
			   'due_margin_bottom': 20,
			   'due_late_margin_top': 8,
			   'due_late_margin_bottom': 8,
			   'due_late_padding': 15,
			   'late_min_height': 70,
			   'due_max_height': 286
				};
				
				var count_assignee_late_subtasks = 0;
				
				/**
	       * Return due bottom position
	       * 
	       * @param Object data
	       * @return integer
	       */
	      var due_position_from_bottom = function(data) {
	      	var bottom = constants.due_margin_bottom;
	      	
	      	if(data['late'].length) {
	      		var height_diff = late_height(data) - constants.due_max_height;
	      		
	      		if(height_diff > 0) {
	      			bottom = bottom + late_height(data) + constants.due_late_padding - height_diff;
	      		} else {
	      			bottom = bottom + late_height(data) + constants.due_late_padding;
	      		} // if
	      	} // if
	      	
	        return bottom;
	      }; // due_position_from_bottom
	      
	      /**
	       * Return late height
	       * 
	       * @param Object data
	       * @return integer
	       */
	      var late_height = function(data) {
	        return (data['late'].length + count_assignee_late_subtasks) * constants.assignment_height + constants.due_late_margin_top + constants.due_late_margin_bottom;
	      }; // late_height
				
				/**
	       * Return label pill
	       * 
	       * @param Number label_id
	       * @return string
	       */
	      var render_label_tab = function(label_id) {
	        return label_id && typeof(data['labels'][label_id]) == 'object' ? App.Wireframe.Utils.renderLabelTag(data['labels'][label_id]) : '';
	      }; // render_label_tab
				
				/**
	       * Return assignment URL
	       * 
	       * @param Object assignment
	       * @return string
	       */
	      var assignment_url = function(assignment) {
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
	       * @return string
	       */
	      var subtask_url = function(assignment, subtask) {
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
	       * Return tracked time
	       * 
	       * @param Number tracked_time
	       * @param Number estimated_time
	       * @return string
	       */
	      var render_tracked_vs_estimated_time = function(tracked_time, estimated_time) {
	      	tracked_time = tracked_time ? tracked_time : 0;
	      	estimated_time = estimated_time ? estimated_time : 0;
	      	
	      	return '<span class="tracked_vs_estimated_time pill"><span class="left_half">' + tracked_time + '</span><span class="right_half">' + estimated_time + '</span></span>';
	      }; // render_tracked_vs_estimated_time
	      
	      /**
	       * Return assignee row
	       * 
	       * @param Object assignee_data
	       * @return JSON
	       */
	      var render_assignee_row = function(assignee_data) {
	      	// Open assignee block and prepare avatar
	  			assignee = '<div class="assignee"><div class="avatar">';
	  			assignee += '<a href="' + assignee_data['view_url'] + '"><img src="' + assignee_data['avatar_url'] + '"></a>';
	  			
	  			// Name
	  			assignee += '</div><div class="name">';
	  			assignee += '<a href="' + assignee_data['view_url'] + '" class="quick_view_item">' + App.excerpt(App.clean(assignee_data['label']), 21) + '</a>';
	  			
	  			// Estimated vs tracked time
	  			assignee += '</div>' + render_estimated_vs_tracked_time(assignee_data) + '</div>';
					
					return {
						id: assignee_data['id'],
						rendered: assignee
					};
	      }; // render_assignee_row
	      
	      /**
	       * Return estimated vs tracked time
	       * 
	       * @param Object assignee_data
	       * @return string
	       */
	      var render_estimated_vs_tracked_time = function(assignee_data) {
	      	classes = 'estimated_vs_tracked_time';
	      	
	      	if(!assignee_data['count_estimated_time']) {
				    classes += ' ok';
				  } else if(assignee_data['count_estimated_time'] && (assignee_data['count_estimated_time'] <= assignee_data['count_tracked_time'])) {
				    classes += ' underestimated';
				  } else {
				  	var round = 10;
				  	var percentage = (assignee_data['count_tracked_time'] / assignee_data['count_estimated_time']) * 100;
				  	var rounded = Math.ceil(percentage / round) * round;
				  	
				  	if(rounded < 90) {
				  		classes += ' ok';
				  	} else if(rounded >= 90) {
				  		classes += ' close_to_estimated_time';
				  	} // if
				  } // if
	      	
	      	return '<div class="' + classes + '"><span class="tracked_time">' + assignee_data['count_tracked_time'] + '</span>/<span class="estimated_time">' + assignee_data['count_estimated_time'] + '</span></div>';
	      }; // render_estimated_vs_tracked_time
	      
	      /**
	       * Return assignment row
	       * 
	       * @param Object assignment_data
	       * @return string
	       */
	      var render_assignment_row = function(assignment_data) {
	      	var assignment_url_base = assignment_url(assignment_data);
	      	
	      	// Prepare label
					var row = render_label_tab(assignment_data['label_id']);
					
					// Prepare name, tracked time and drag handler
					row += '<span class="assignment_name"><a href="' + assignment_url_base + '" class="quick_view_item">' + App.excerpt(App.clean(assignment_data['name']), 34) + '</a></span>';
					row += render_tracked_vs_estimated_time(assignment_data['tracked_time'], assignment_data['estimated_time']);
					row += '<span class="drag_handle" style="display: none;"></span>';
					
					return row;
	      }; // render_assignment_row
	      
	      /**
	       * Return subtask row
	       * 
	       * @param Object assignment_data
	       * @param Object subtask_data
	       * @return string
	       */
	      var render_subtask_row = function(assignment_data, subtask_data) {
	      	var subtask_url_base = subtask_url(assignment_data, subtask_data);
	      	
	      	// Prepare label
					var row = render_label_tab(subtask_data['label_id']);
					
					// Prepare name and tracked time
					row += '<span class="assignment_name"><a href="' + subtask_url_base + '" class="quick_view_item">' + App.excerpt(App.clean(subtask_data['body']), 31) + '</a></span>';
					
					return row;
	      }; // render_subtask_row

        /**
         * Return assignment due on timestamp
         *
         * @param Object assignment_data
         * @return Number
         */
        var get_due_on_timestamp = function(assignment_data) {
          var due_on_timestamp = 0;

          if(assignment_data.due_on) {
            due_on_timestamp = assignment_data.due_on.timestamp;
          } // if

          return due_on_timestamp;
        }; // get_due_on_timestamp

        /**
         * Manually cut unwanted decimals off of a number
         *
         * @param Number val
         * @return Number
         */
        var cut_decimals_off = function(val) {
          if(val.charAt(val.length-1) == '0') { // xx.00
            val = val.substr(0, val.length-1);
          } // if
          if(val.charAt(val.length-1) == '0') { // xx.0
            val = val.substr(0, val.length-1);
          } // if
          if(val.charAt(val.length-1) == '.') { // xx.
            val = val.substr(0, val.length-1);
          } // if
          return val;
        }; // cut_decimals_off
	      
	      /**
		     * Block form while filter is being executed
		     */
		    var block_form = function(workload_wrapper, form_head) {
		      workload_wrapper.find('.items_wrapper').empty().parent().parent().append('<img src="' + App.Wireframe.Utils.indicatorUrl() + '">').addClass('loading')
	    		form_head.find('select, input, button').prop('disabled', true);
	    		
	    		if(workload_wrapper.find('.empty_page').length) {
	      		workload_wrapper.find('.empty_page').remove();
	      	} // if
		    }; // block_form
		    
		    /**
		     * Unblock form after filter has been executed
		     */
		    var unblock_form = function(workload_wrapper, form_head) {
		      workload_wrapper.find('.items_wrapper').empty().parent().parent().removeClass('loading').find('img').remove();
					form_head.find('select, input, button').prop('disabled', false);
	      	
	      	if(workload_wrapper.find('.empty_page').length) {
	      		workload_wrapper.find('.empty_page').remove();
	      	} // if
		    }; // unblock_form
	      
	      /**
	       * Return the whole workload
	       * 
	       * @param Object workload_wrapper
	       * @param Object workload
	       * @return string
	       */
	      var render_workload = function(workload_wrapper, workload) {
	      	if(typeof(workload['assignees']) == 'object') {
	      		var items_wrapper = workload_wrapper.find('.items_wrapper');

	      		var count_assignees = 0;
						for(i in workload['assignees']) {
					    if(workload['assignees'].hasOwnProperty(i) && workload['assignees'][i]['label'] != 'Unassigned' && typeof(workload['real_assignees'][i]) == 'object') {
					    	count_assignees++;
					    } // if
						} // for

						var items_wrapper_width = count_assignees * constants.item_width;
	      		items_wrapper.css('width', items_wrapper_width);
	      		
	      		var shared_assignments = new Array();
	      		
	      		var item_order = 0;
	      		App.each(workload['assignees'], function(assignee_key, assignee_data) {
	      			
	      			// Jump over unknown user (assignment filter returns it for unassigned tasks/subtasks)
	      			if(assignee_key != 'unknown-user') {
	      				// Count assignee's late subtasks
		      			if(assignee_data['late'].length) {
		      				count_assignee_late_subtasks = 0;
		      				
		      				App.each(assignee_data['late'], function(id, data) {
		      					if(typeof(data['subtasks']) != 'undefined') {
											for(i in data['subtasks']) {
										    if(data['subtasks'].hasOwnProperty(i)) {
										    	count_assignee_late_subtasks++;
										    } // if
											} // for
		      					} // if
		      				});
		      			} // if
		      			
		      			var item = $('<div class="item"></div>');
		      			
		      			var item_position_from_left = item_order * constants.item_width;
		      			item.css('left', item_position_from_left);

		      			assignee = render_assignee_row(assignee_data);
		      			
		      			item.attr('user_id', assignee.id);
		      			item.append(assignee.rendered);
		      			
		      			// Open due block and set position from the bottom
		      			var due = $('<div class="due" style="min-height: ' + constants.late_min_height + 'px; bottom: ' + due_position_from_bottom(assignee_data) + 'px;"><div class="due_inner"></div></div>');
		      			var due_inner = due.find('.due_inner');
		      			
		      			due_inner.css('bottom', due_position_from_bottom(assignee_data));
		      			
		    				var assignment_order = 0;
		      			App.each(assignee_data['assignments'], function(assignment_id, assignment_data) {
		    					// Open assignment block and set position from the top
		      				var assignment = $('<div class="assignment" assignee_id="' + assignment_data.assignee_id + '" real_id="' + assignment_data.id + '" init_user_id="' + assignee.id + '" init_type="assignments" task_id="' + assignment_data.task_id + '" due_on_timestamp="' + get_due_on_timestamp(assignment_data) + '" project_id="' + assignment_data.project_id + '"></div>');
		      				assignment.css('top', assignment_order * constants.assignment_height + constants.due_late_margin_top);
		      				
		      				var row = render_assignment_row(assignment_data);
		      				assignment.append(row).appendTo(due_inner);
		      				
		      				assignment_order++;
		      				
		      				// Subtasks
		              if(typeof(assignment_data['subtasks']) != 'undefined') {
		              	App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
		              		var not_assigned_class = assignee.id != subtask_data.assignee_id ? 'not_assigned' : '';
		              		
		              		// Collect shared assignments for including them later on per user according to user_id
		              		if(assignee.id != subtask_data.assignee_id) {
		              			shared_assignments.push({ 'id' : assignment_data.id, 'type' : 'assignments', 'assignee_id' : assignee.id, 'user_id' : subtask_data.assignee_id });
		              		} // if
		              		
		              		// Open assignment block and set position from the top
				      				var assignment = $('<div class="assignment subtask ' + not_assigned_class + '" assignee_id="' + subtask_data.assignee_id + '" subtask_id="' + subtask_id + '" due_on_timestamp="' + subtask_data.due_on.timestamp + '" task_id="' + assignment_data.task_id + '" project_id="' + assignment_data.project_id + '"></div>');
				      				assignment.css('top', assignment_order * constants.assignment_height + constants.due_late_margin_top);
				      				
				      				var row = render_subtask_row(assignment_data, subtask_data);
				      				assignment.append(row).appendTo(due_inner);
				      				
				      				assignment_order++;
		              	});
		              } // if
		      			});

		      			item.append(due);
		      			
		      			if(assignee_data['late'].length) {
		      				// Open late block and set height
			      			var late = $('<div class="late" style="max-height: ' + constants.due_max_height + 'px;"><div class="late_inner"></div></div>');
			      			var late_inner = late.find('.late_inner');
			      			
			      			late_inner.css('height', late_height(assignee_data));
			      			
			      			var late_order = assignee_data['late'].length + count_assignee_late_subtasks - 1;
			      			App.each(assignee_data['late'], function(late_id, late_data) {
			      				var height_diff = late_height(assignee_data) - constants.due_max_height;
			      				
			    					// Open late block and set position from the bottom
			      				var assignment = $('<div class="assignment" assignee_id="' + late_data.assignee_id + '" real_id="' + late_data.id + '" init_user_id="' + assignee.id + '" init_type="late" task_id="' + late_data.task_id + '" due_on_timestamp="' + get_due_on_timestamp(late_data) + '" project_id="' + late_data.project_id + '"></div>');
			      				
			      				if(height_diff > 0) {
			      					assignment.css('bottom', late_order * constants.assignment_height + constants.due_late_margin_top - height_diff);
			      				} else {
			      					assignment.css('bottom', late_order * constants.assignment_height + constants.due_late_margin_top);
			      				} // if
			      				
			      				var row = render_assignment_row(late_data);
			      				assignment.append(row).appendTo(late_inner);
			      				
			      				late_order--;
			      				
			      				// Subtasks
			              if(typeof(late_data['subtasks']) != 'undefined') {
			              	App.each(late_data['subtasks'], function(subtask_id, subtask_data) {
			              		var not_assigned_class = assignee.id != subtask_data.assignee_id ? 'not_assigned' : '';
			              		
			              		// Collect shared assignments for including them later on per user according to user_id
			              		if(assignee.id != subtask_data.assignee_id) {
			              			shared_assignments.push({ 'id' : late_data.id, 'type' : 'late', 'assignee_id' : assignee.id, 'user_id' : subtask_data.assignee_id });
			              		} // if
			              		
			              		// Open late block and set position from the bottom
					      				var assignment = $('<div class="assignment subtask ' + not_assigned_class + '" assignee_id="' + subtask_data.assignee_id + '" subtask_id="' + subtask_id + '" due_on_timestamp="' + subtask_data.due_on.timestamp + '" task_id="' + late_data.task_id + '" project_id="' + late_data.project_id + '"></div>');
					      				
					      				if(height_diff > 0) {
					      					assignment.css('bottom', late_order * constants.assignment_height + constants.due_late_margin_top - height_diff);
					      				} else {
					      					assignment.css('bottom', late_order * constants.assignment_height + constants.due_late_margin_top);
					      				} // if
					      				
					      				var row = render_subtask_row(late_data, subtask_data);
					      				assignment.append(row).appendTo(late_inner);
					      				
					      				late_order--;
			              	});
			              } // if
			      			});
			      			
			      			item.append(late);
		      			} // if

                if(typeof(workload['real_assignees'][assignee_key]) == 'object') {
                  item.appendTo(items_wrapper);
                  item_order++;
                } // if
		      			
		      		// Render unassignments from "unknown user" to assigned users appropriately
	      			} else {
	      				// Render due assignments (if any)
		      			App.each(assignee_data['assignments'], function(assignment_id, assignment_data) {
		    					if(typeof(assignment_data['subtasks']) != 'undefined') {
		    						var user_ids = new Array();
		    						
		              	// Collect all user ids first
		    						App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
		              		user_ids.push(subtask_data.assignee_id);
		              	});
		              	
		              	if(user_ids.length) {
		              		// Make array distincted
		              		user_ids = user_ids.filter(function(itm, i, user_ids) {
												return i == user_ids.indexOf(itm);
											});
											
											// Append assignments to users respectively
			              	App.each(user_ids, function(k, user_id) {
			              		share_due_tasks(assignment_data, user_id);
			              	});
		              	} // if
		              } // if
		      			});
		      			
		      			// Render late assignments (if any)
		      			if(assignee_data['late'].length) {
		      				App.each(assignee_data['late'], function(late_id, late_data) {
			    					if(typeof(late_data['subtasks']) != 'undefined') {
			    						var user_ids = new Array();
			              	
			    						// Collect all user ids first and count subtasks
			    						var count_late_subtasks = 0;
			              	App.each(late_data['subtasks'], function(subtask_id, subtask_data) {
			              		user_ids.push(subtask_data.assignee_id);
			              		count_late_subtasks++;
			              	});
			              	
			              	if(user_ids.length) {
			              		// Make array distincted
			              		user_ids = user_ids.filter(function(itm, i, user_ids) {
													return i == user_ids.indexOf(itm);
												});
												
												// Append assignments to users respectively
			              		App.each(user_ids, function(k, user_id) {
			              			share_late_tasks(late_data, user_id, count_late_subtasks);
			              		});
			              	} // if
			              } // if
		      				});
		      			} // if
	      			} // if
	      		});
	      		
	      		// Loop through shared assignments and append to all other users accordingly
	      		if(shared_assignments.length) {
	      			shared_assignments = distinct(shared_assignments);
	      			
	      			App.each(shared_assignments, function(k, shared_assignment) {
	      				var assignment_data;
	      				
	      				// Due shared assignments
      					if(shared_assignment.type == 'assignments') {
      						assignment_data = workload['assignees']['user-' + shared_assignment.assignee_id][shared_assignment.type][shared_assignment.id];
      						
      						if(typeof(assignment_data) != 'undefined') {
      							share_due_tasks(assignment_data, shared_assignment.user_id);
      						} // if
      						
      					// Late shared assignments
      					} else if(shared_assignment.type == 'late') {
      						App.each(workload['assignees']['user-' + shared_assignment.assignee_id][shared_assignment.type], function(late_id, late_data) {
      							if(late_id == shared_assignment.id) {
      								assignment_data = late_data;
      							} // if
      						});
      						
      						if(typeof(assignment_data) != 'undefined') {
	      						var count_late_subtasks = 0;
		              	App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
		              		count_late_subtasks++;
		              	});
		              	
      							share_late_tasks(assignment_data, shared_assignment.user_id, count_late_subtasks);
      						} // if
      					} // if
	      			});
	      		} // if
	      		
	      		init_task_reassignment(workload_wrapper, workload);
	      	};
	      }; // render_workload
	      
	      /**
		     * Init task reassignment (drag & drop functionality)
		     *
		     * @param Object workload_wrapper
		     * @param Object workload
		     */
		    var init_task_reassignment = function(workload_wrapper, workload) {
		      // Define drag handler behavior
      		var assignments = workload_wrapper.find('.assignment').not('.subtask').not('[shared]');
			    var drag_handlers = assignments.find('.drag_handle');
			    
		      if(assignments.length) {
		        assignments.hover(function() {
		        	var assignment_over = $(this);
		        	
		          assignment_over.find('.tracked_vs_estimated_time').hide();
		          assignment_over.find('.drag_handle').show();
		        }, function() {
		        	var assignment_out = $(this);
		        	
		          assignment_out.find('.tracked_vs_estimated_time').show();
		          assignment_out.find('.drag_handle').hide();
		        });
		      } // if
		      
		      var orig_assignment_user_id;
		      var orig_assignment_wrapper_class;

		      // Define draggable assignment widget
		      assignments.draggable({
		      	helper : 'clone',
		      	appendTo : results_wrapper.find('.items_wrapper'),
		      	handle : drag_handlers,
		      	zIndex : 3,
		      	revert : 'invalid',
		      	start : function(e, ui) {
		      		var orig_assignment = $(this);
		      		var clone = ui.helper;
		      		
		      		orig_assignment_user_id = orig_assignment.closest('.item').attr('user_id');
		      		orig_assignment_wrapper_class = orig_assignment.parent().parent().attr('class');
		      		
		      		var drag_class = orig_assignment_wrapper_class == 'due' ? 'drag_due' : 'drag_late';
		      		
		      		clone.addClass(drag_class);
		      	},
		      	stop : function(e, ui) {
		      		// Revert back background colors for previously non-accepted items
		      		$('.item .due').css('background-color', '#FFFFFF');
		      		$('.item .late').css('background-color', '#FFE8E8');
		      	}
		      });
		      
		      $('.item').droppable({
		      	accept : function(assignment) {
		      		var item = $(this);
		      		var user_id = item.attr('user_id');
		      		var project_id = assignment.attr('project_id');
		      		
		      		var user_assigned_to_project = data['project_users'] && typeof(data['project_users'][user_id]) != 'undefined' && typeof(data['project_users'][user_id][project_id]) != 'undefined' ? true : false;
		      		
		      		if(user_assigned_to_project) {
		      			return true;
		      		} else {
		      			// Change background color of non-accepted items
		      			item.find('.due, .late').css('background-color', '#F1F1F1');
		      			
			      		return false;
		      		} // if
		      	},
			      drop : function(e, ui) {
			      	var orig_assignment = $(ui.draggable);
			      	var orig_assignment_real_id = orig_assignment.attr('real_id');
			      	var orig_assignment_init_user_id = orig_assignment.attr('init_user_id');
			      	var orig_assignment_init_type = orig_assignment.attr('init_type');
			      	var orig_assignment_id = orig_assignment.attr('task_id');
			      	var orig_assignee_id = orig_assignment.attr('assignee_id');

			      	var project_id = orig_assignment.attr('project_id');
			      	
			      	var project_slug = data['project_slugs'] && typeof(data['project_slugs'][project_id]) == 'string' ? data['project_slugs'][project_id] : null;
			      	var reassign_url = data['reassign_url'].replace('--PROJECT_SLUG--', project_slug).replace('--TASK_ID--', orig_assignment_id);
			      	
			      	// Assignment moved to item wrapper
			      	var current_item = $(this);
			      	var current_assignments_wrapper = current_item.find('.' + orig_assignment_wrapper_class + ' .' + orig_assignment_wrapper_class + '_inner');
			      	var current_assignments = current_assignments_wrapper.find('.assignment');
			      	
			      	// Opposite assignment type against current one
			      	var other_assignments_wrapper = orig_assignment_wrapper_class == 'due' ? current_item.find('.late') : current_item.find('.due');
			      	
			      	var new_assignee_id = current_item.attr('user_id');

			      	// Find other assignees
			      	var other_assignees = null;
			      	if(orig_assignment_init_type != 'late') {
			      		other_assignees = workload['assignees']['user-' + orig_assignment_init_user_id][orig_assignment_init_type][orig_assignment_real_id]['other_assignees'];
			      	} else {
			      		App.each(workload['assignees']['user-' + orig_assignment_init_user_id][orig_assignment_init_type], function(late_id, late_data) {
	  							if(late_id == orig_assignment_real_id) {
	  								other_assignees = late_data.other_assignees;
	  							} // if
	  						});
			      	} // if
			      	
			      	// Task can be reassigned only to other user while reassignment URL is valid
			      	if(new_assignee_id != orig_assignee_id && orig_assignee_id && project_slug) {
				      	$.ajax({
		              'url' : reassign_url,
		              'type' : 'POST',
		              'data' : { 'object' : { 'assignee_id' : new_assignee_id }, 'object[other_assignees]' : other_assignees, 'object[other_assignees][]' : orig_assignment_init_user_id, 'submitted' : 'submitted' },
		              'success' : function(response) {
		              	var orig_assignments_wrapper = $('.item[user_id=' + orig_assignment_user_id + '] .' + orig_assignment_wrapper_class + ' .' + orig_assignment_wrapper_class + '_inner');
		              	var orig_assignment_subtasks = orig_assignments_wrapper.find('.subtask[project_id=' + project_id + '][task_id=' + orig_assignment_id + ']');

                    var assignment_already_exists = false;
                    var shared_assignment_user_ids = new Array();
		              	
		              	orig_assignment.attr('assignee_id', new_assignee_id);

                    if(current_assignments.length) {
                      App.each(current_assignments, function(k, current_assignment) {
                        // If shared task already exists remove it with all subtasks within
                        if(orig_assignment_real_id == $(current_assignment).attr('real_id')) {
                          assignment_already_exists = true;

                          $(current_assignment).remove();

                          var current_assignment_subtasks = current_assignments_wrapper.find('.subtask[project_id=' + project_id + '][task_id=' + orig_assignment_id + ']');
                          if(current_assignment_subtasks.length) {
                            current_assignment_subtasks.remove();
                          } // if

                          // Recalculate current assignments
                          current_assignments = current_assignments_wrapper.find('.assignment');
                        } // if
                      });
                    } // if

		              	// Due assignment
		              	if(orig_assignment_wrapper_class == 'due') {
		              		if(current_assignments.length) {
                        // Rearange current assignments (due to removal of existed one) prior adding original one
                        if(assignment_already_exists) {
                          var current_assignment_order = 0;
                          App.each(current_assignments, function(k, current_assignment) {
                            $(current_assignment).css('top', constants.due_late_margin_top + current_assignment_order * constants.assignment_height);
                            current_assignment_order++;
                          });
                        } // if

							      		orig_assignment.css('top', constants.due_late_margin_top + current_assignments.length * constants.assignment_height);
							      	} else {
							      		orig_assignment.css('top', constants.due_late_margin_top);
							      	} // if
							      	
					            // Append reassignment to new user first
							      	current_assignments_wrapper.append(orig_assignment);
							      	
			            		// Then append (copy/paste) reassignment's subtasks to it
					            if(orig_assignment_subtasks.length) {
			            			var orig_assignment_subtask_order = 1;
			            			App.each(orig_assignment_subtasks, function(k, orig_assignment_subtask) {
			            				var clone = $(orig_assignment_subtask).clone();
			            				var clone_assignee_id = clone.attr('assignee_id');
			            				
			            				if(clone_assignee_id == new_assignee_id) {
			            					clone.removeClass('not_assigned');
			            				} else {
			            					clone.addClass('not_assigned');
                            shared_assignment_user_ids.push({ 'id' : clone_assignee_id });
			            				} // if
				            			
				            			clone.css('top', constants.due_late_margin_top + (current_assignments.length + orig_assignment_subtask_order) * constants.assignment_height);
				            			current_assignments_wrapper.append(clone);
				            			
				            			$(orig_assignment_subtask).remove();
				            			orig_assignment_subtask_order++;
			            			});
			            		} // if

                      // Re-render shared original assignment to appropriate users
                      if(shared_assignment_user_ids.length) {
                        shared_assignment_user_ids = distinct(shared_assignment_user_ids);

                        App.each(shared_assignment_user_ids, function(k, shared_assignment_user_id) {
                          var assignment_data = workload['assignees']['user-' + orig_assignment_init_user_id][orig_assignment_init_type][orig_assignment_real_id]

                          if(typeof(assignment_data) != 'undefined') {
                            share_due_tasks(assignment_data, shared_assignment_user_id.id);
                          } // if
                        });
                      } // if
					            
			            		var orig_assignments = orig_assignments_wrapper.find('.assignment');
					            
					            // Rearrange assignments from original position
			            		if(orig_assignments.length) {
							      		var orig_assignment_order = 0;
					            	App.each(orig_assignments, function(k, orig_assignment) {
							      			$(orig_assignment).css('top', constants.due_late_margin_top + orig_assignment_order * constants.assignment_height);
							      			orig_assignment_order++;
							      		});
							      	} // if
							      	
							      // Late assignment
		              	} else {
		              		if(current_assignments.length) {
                        // Rearange current assignments (due to removal of existed one) prior adding original one
                        if(assignment_already_exists) {
                          var current_late_order = current_assignments.length - 1;
                          App.each(current_assignments, function(k, current_assignment) {
                            $(current_assignment).css('bottom', constants.due_late_margin_bottom + current_late_order * constants.assignment_height);
                            current_late_order--;
                          });

                          current_assignments_wrapper.css('height', constants.due_late_margin_bottom + current_assignments.length * constants.assignment_height + constants.due_late_margin_top);

                          other_assignments_wrapper.css('bottom', constants.due_margin_bottom + constants.due_late_margin_bottom + current_assignments.length * constants.assignment_height + constants.due_late_margin_top + constants.due_late_padding);
                          other_assignments_wrapper.find('div:first-child').css('bottom', constants.due_margin_bottom + constants.due_late_margin_bottom + current_assignments.length * constants.assignment_height + constants.due_late_margin_top + constants.due_late_padding);
                        } // if

                        var current_assignments_height = constants.due_late_margin_bottom + (orig_assignment_subtasks.length + 1) * constants.assignment_height + current_assignments.length * constants.assignment_height + constants.due_late_margin_top;
                        var other_assignments_bottom = constants.due_margin_bottom + current_assignments_height + constants.due_late_padding;
                        var height_diff = current_assignments_height - constants.due_max_height;

                        if(height_diff > 0) {
                          other_assignments_bottom = other_assignments_bottom - height_diff;
                        } // if

		              			// Calculate new due assignment height first
		              			other_assignments_wrapper.css('bottom', other_assignments_bottom);
		              			other_assignments_wrapper.find('div:first-child').css('bottom', other_assignments_bottom);

		              			current_assignments_wrapper.css('height', current_assignments_height);

		              			// Then prepend (cut) reassignment's subtasks to new user
		              			cut_orig_subtasks(current_assignments_wrapper, current_assignments, orig_assignment_subtasks, new_assignee_id, shared_assignment_user_ids, height_diff);

                        var orig_assignment_bottom = constants.due_late_margin_bottom + (orig_assignment_subtasks.length + current_assignments.length) * constants.assignment_height;
                        if(height_diff > 0) {
                          orig_assignment_bottom = orig_assignment_bottom - height_diff;
                        } // if

                        orig_assignment.css('bottom', orig_assignment_bottom);

                        // Rearrange existing late assignments
                        if(height_diff > 0) {
                          var current_late_order = current_assignments.length - 1;
                          App.each(current_assignments, function(k, current_assignment) {
                            $(current_assignment).css('bottom', constants.due_late_margin_bottom + current_late_order * constants.assignment_height - height_diff);
                            current_late_order--;
                          });
                        }
							      	} else {
                        var current_assignments_height = constants.due_late_margin_bottom + (orig_assignment_subtasks.length + 1) * constants.assignment_height + constants.due_late_margin_top;
                        var other_assignments_bottom = constants.due_margin_bottom + current_assignments_height + constants.due_late_padding;
                        var height_diff = current_assignments_height - constants.due_max_height;

                        if(height_diff > 0) {
                          other_assignments_bottom = other_assignments_bottom - height_diff;
                        } // if

							      		other_assignments_wrapper.css('bottom', other_assignments_bottom);
		              			other_assignments_wrapper.find('div:first-child').css('bottom', other_assignments_bottom);
							      		
							      		// Create new late wrapper when there are no already such assignments
		              			var current_late = $('<div class="late" style="max-height: ' + constants.due_max_height + 'px;"><div class="late_inner"></div></div>');
							      		
						      			current_assignments_wrapper = current_late.find('.late_inner');
						      			current_assignments_wrapper.css('height', current_assignments_height);
						      			
						      			current_item.append(current_late);
				            		
				            		// Prepend (cut) reassignment's subtasks to new user
						      			cut_orig_subtasks(current_assignments_wrapper, current_assignments, orig_assignment_subtasks, new_assignee_id, shared_assignment_user_ids, height_diff);

                        var orig_assignment_bottom = constants.due_late_margin_bottom + orig_assignment_subtasks.length * constants.assignment_height;
                        if(height_diff > 0) {
                          orig_assignment_bottom = orig_assignment_bottom - height_diff;
                        } // if

                        orig_assignment.css('bottom', orig_assignment_bottom);
							      	} // if
							      	
							      	// Append reassignment to new user
							      	current_assignments_wrapper.prepend(orig_assignment);
							      	
							      	var orig_assignments = orig_assignments_wrapper.find('.assignment');
							      	var orig_due_assignments_wrapper = $('.item[user_id=' + orig_assignment_user_id + '] .due');

					            // Rearrange assignments from original position
							      	if(orig_assignments.length) {
                        var orig_assignments_height = constants.due_late_margin_bottom + orig_assignments.length * constants.assignment_height + constants.due_late_margin_top;
                        var orig_assignments_bottom = constants.due_margin_bottom + orig_assignments_height + constants.due_late_padding;
                        var height_diff = orig_assignments_height - constants.due_max_height;

                        if(height_diff > 0) {
                          orig_assignments_bottom = orig_assignments_bottom - height_diff;
                        } // if

							      		orig_assignments_wrapper.css('height', orig_assignments_height);

							      		orig_due_assignments_wrapper.css('bottom', orig_assignments_bottom);
							      		orig_due_assignments_wrapper.find('div:first-child').css('bottom', orig_assignments_bottom);

                        // Rearrange existing late assignments
                        if(height_diff > 0) {
                          var orig_late_order = orig_assignments.length - 1;
                          App.each(orig_assignments, function(k, orig_assignment) {
                            $(orig_assignment).css('bottom', constants.due_late_margin_bottom + orig_late_order * constants.assignment_height - height_diff);
                            orig_late_order--;
                          });
                        } else {
                          var orig_late_order = orig_assignments.length - 1;
                          App.each(orig_assignments, function(k, orig_assignment) {
                            $(orig_assignment).css('bottom', constants.due_late_margin_bottom + orig_late_order * constants.assignment_height);
                            orig_late_order--;
                          });
                        }
							      	} else {
							      		orig_assignments_wrapper.parent().remove();

							      		orig_due_assignments_wrapper.css('bottom', constants.due_margin_bottom);
							      		orig_due_assignments_wrapper.find('div:first-child').css('bottom', constants.due_margin_bottom);
							      	} // if

                      // Re-render shared original assignment to appropriate users
                      if(shared_assignment_user_ids.length) {
                        shared_assignment_user_ids = distinct(shared_assignment_user_ids);

                        App.each(shared_assignment_user_ids, function(k, shared_assignment_user_id) {
                          var assignment_data;

                          App.each(workload['assignees']['user-' + orig_assignment_init_user_id][orig_assignment_init_type], function(late_id, late_data) {
                            if(late_id == orig_assignment_real_id) {
                              assignment_data = late_data;
                            } // if
                          });

                          if(typeof(assignment_data) != 'undefined') {
                            var count_late_subtasks = 0;
                            App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
                              count_late_subtasks++;
                            });

                            share_late_tasks(assignment_data, shared_assignment_user_id.id, count_late_subtasks);
                          } // if
                        });
                      } // if
		              	} // if
		              	
		              	var current_time_tracking_wrapper = current_item.find('.assignee .estimated_vs_tracked_time');
		              	recalculate_tracked_vs_estimated_values(orig_assignment, current_time_tracking_wrapper, true);
		              	
		              	var orig_time_tracking_wrapper = $('.item[user_id=' + orig_assignment_user_id + '] .assignee .estimated_vs_tracked_time');
						      	recalculate_tracked_vs_estimated_values(orig_assignment, orig_time_tracking_wrapper, false);
		              },
		              'error' : function(response) {
		                App.Wireframe.Flash.error(App.lang('Failed to reassign Task.'));
		              }
		            });
			      	} // if
			      }
			    });
		    }; // init_task_reassignment
		    
		    /**
		     * Distinct shared assignments array
		     *
		     * @param Object shared_assignments
		     * @return Object
		     */
	      var distinct = function(shared_assignments) {
					var array_with_unique_values = [];
					var object_counter = {};
					
					for(i = 0; i < shared_assignments.length; i++) {
				    var current_member_of_array_key = JSON.stringify(shared_assignments[i]);
				    var current_member_of_array_value = shared_assignments[i];
				    
				    if(object_counter[current_member_of_array_key] === undefined) {
			        array_with_unique_values.push(current_member_of_array_value);
			        object_counter[current_member_of_array_key] = 1;
				    } else {
				    	object_counter[current_member_of_array_key]++;
				    } // if
					} // for
					
					return array_with_unique_values;
	      };
	      
	      /**
		     * Share late tasks across all user columns accordingly
		     *
		     * @param Object assignment_data
		     * @param Integer user_id
		     * @return null
		     */
		    var share_late_tasks = function(late_data, user_id, count_late_subtasks) {
      		var user_wrapper = $('.item[user_id=' + user_id + ']');
    			var user_due_wrapper = user_wrapper.find('.due');
    			var user_late_wrapper = user_wrapper.find('.late .late_inner');
    			var user_late_assignments_count = user_late_wrapper.find('.assignment').length;
    			
    			if(user_late_assignments_count) {
            App.each(user_late_wrapper.find('.assignment'), function(k, current_assignment) {
              // If shared task already exists remove it with all subtasks within
              if(late_data.id == $(current_assignment).attr('real_id')) {
                $(current_assignment).remove();

                var current_assignment_subtasks = user_late_wrapper.find('.subtask[project_id=' + late_data.project_id + '][task_id=' + late_data.task_id + ']');
                if(current_assignment_subtasks.length) {
                  current_assignment_subtasks.remove();
                } // if

                // Recalculate current assignments
                user_late_assignments_count = user_late_wrapper.find('.assignment').length;
              } // if
            });

            var late_height = constants.due_late_margin_bottom + (count_late_subtasks + 1) * constants.assignment_height + user_late_assignments_count * constants.assignment_height + constants.due_late_margin_top;
            var due_bottom = constants.due_margin_bottom + late_height + constants.due_late_padding;
            var height_diff = late_height - constants.due_max_height;

            if(height_diff > 0) {
              due_bottom = due_bottom - height_diff;
            } // if

      			user_due_wrapper.css('bottom', due_bottom);
      			user_due_wrapper.find('div:first-child').css('bottom', due_bottom);

      			user_late_wrapper.css('height', late_height);

						share_common_tasks(late_data, user_id, count_late_subtasks, user_late_assignments_count, user_late_wrapper, height_diff);
            share_common_subtasks(late_data, user_id, count_late_subtasks, user_late_assignments_count, user_late_wrapper, height_diff);

            // Rearrange existing late assignments
            if(height_diff > 0) {
              var late_assignments = user_late_wrapper.find('.assignment');
              var current_late_order = late_assignments.length - 1;

              App.each(late_assignments, function(k, late) {
                $(late).css('bottom', constants.due_late_margin_bottom + current_late_order * constants.assignment_height - height_diff);
                current_late_order--;
              });
            }
	      	} else {
            var late_height = constants.due_late_margin_bottom + (count_late_subtasks + 1) * constants.assignment_height + constants.due_late_margin_top;
            var due_bottom = constants.due_margin_bottom + late_height + constants.due_late_padding;
            var height_diff = late_height - constants.due_max_height;

            if(height_diff > 0) {
              due_bottom = due_bottom - height_diff;
            } // if

	      		user_due_wrapper.css('bottom', due_bottom);
      			user_due_wrapper.find('div:first-child').css('bottom', due_bottom);
	      		
	      		// Create new late wrapper when there are no already such assignments
      			var current_late = $('<div class="late" style="max-height: ' + constants.due_max_height + 'px;"><div class="late_inner"></div></div>');
	      		
      			user_late_wrapper = current_late.find('.late_inner');
      			user_late_wrapper.css('height', late_height);
      			
      			user_wrapper.append(current_late);

          	share_common_tasks(late_data, user_id, count_late_subtasks, user_late_assignments_count, user_late_wrapper, height_diff);
            share_common_subtasks(late_data, user_id, count_late_subtasks, user_late_assignments_count, user_late_wrapper, height_diff);
	      	} // if
		    }; // share_late_tasks
	      
	      /**
		     * Share assigned tasks across all user columns accordingly
		     *
		     * @param Object assignment_data
		     * @param Integer user_id
		     * @return null
		     */
		    var share_due_tasks = function(assignment_data, user_id) {
      		var user_due_wrapper = $('.item[user_id=' + user_id + '] .due .due_inner');
      		var user_due_assignments_count = user_due_wrapper.find('.assignment').length;

          App.each(user_due_wrapper.find('.assignment'), function(k, current_assignment) {
            // If shared task already exists remove it with all subtasks within
            if(assignment_data.id == $(current_assignment).attr('real_id')) {
              $(current_assignment).remove();

              var current_assignment_subtasks = user_due_wrapper.find('.subtask[project_id=' + assignment_data.project_id + '][task_id=' + assignment_data.task_id + ']');
              if(current_assignment_subtasks.length) {
                current_assignment_subtasks.remove();
              } // if

              // Recalculate current assignments
              user_due_assignments_count = user_due_wrapper.find('.assignment').length;
            } // if
          });
      		
      		// Task
      		var common_assignment_wrapper = $('<div class="assignment not_assigned" shared="true" real_id="' + assignment_data.id + '" init_user_id="' + user_id + '" init_type="assignments" task_id="' + assignment_data.task_id + '" due_on_timestamp="' + get_due_on_timestamp(assignment_data) + '" project_id="' + assignment_data.project_id + '"></div>');
      		var common_assignment = common_assignment_wrapper.append(render_assignment_row(assignment_data));
      		
      		if(user_due_assignments_count) {
	      		common_assignment_wrapper.css('top', constants.due_late_margin_top + user_due_assignments_count * constants.assignment_height);
	      	} else {
	      		common_assignment_wrapper.css('top', constants.due_late_margin_top);
	      	} // if
  				
  				user_due_wrapper.append(common_assignment);
  				
  				// Subtasks
  				var common_subtask_order = 1;
          App.each(assignment_data['subtasks'], function(subtask_id, subtask_data) {
          	var not_assigned_class = user_id != subtask_data.assignee_id ? 'not_assigned' : '';
    				var common_subtask_wrapper = $('<div class="assignment subtask ' + not_assigned_class + '" assignee_id="' + subtask_data.assignee_id + '" subtask_id="' + subtask_id + '" due_on_timestamp="' + subtask_data.due_on.timestamp + '" task_id="' + assignment_data.task_id + '" project_id="' + assignment_data.project_id + '"></div>');
    				var common_subtask = common_subtask_wrapper.append(render_subtask_row(assignment_data, subtask_data));
    				
    				common_subtask_wrapper.css('top', constants.due_late_margin_top + (user_due_assignments_count + common_subtask_order) * constants.assignment_height);
    				
    				user_due_wrapper.append(common_subtask);
    				
    				common_subtask_order++;
        	});
		    }; // share_due_tasks
		    
		    /**
		     * Put unassigned tasks to all users accordingly
		     *
		     * @param Object late_assignment_data
		     * @param Integer subtasks_count
         * @param integer user_id
		     * @param Integer existing_late_assignments_count
		     * @param Object late_wrapper
		     * @return null
		     */
		    var share_common_tasks = function(late_assignment_data, user_id, subtasks_count, existing_late_assignments_count, late_wrapper, height_diff) {
      		var common_assignment_wrapper = $('<div class="assignment not_assigned" shared="true" real_id="' + late_assignment_data.id + '" init_user_id="' + user_id + '" init_type="late" task_id="' + late_assignment_data.task_id + '" due_on_timestamp="' + get_due_on_timestamp(late_assignment_data) + '" project_id="' + late_assignment_data.project_id + '"></div>');
      		var common_assignment = common_assignment_wrapper.append(render_assignment_row(late_assignment_data));
      		
      		var calculated_assignments_length = existing_late_assignments_count ? subtasks_count + existing_late_assignments_count : subtasks_count;

          if(height_diff > 0) {
            common_assignment_wrapper.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height - height_diff);
          } else {
            common_assignment_wrapper.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height);
          } // if
  				
  				late_wrapper.append(common_assignment);
		    }; // share_common_tasks
	      
	      /**
		     * Put unassigned task's subtasks to all users accordingly
		     *
		     * @param Object late_assignment_data
		     * @param Integer user_id
		     * @param Integer subtasks_count
		     * @param Integer existing_late_assignments_count
		     * @param Object late_wrapper
		     * @return null
		     */
		    var share_common_subtasks = function(late_assignment_data, user_id, subtasks_count, existing_late_assignments_count, late_wrapper, height_diff) {
      		var common_subtask_order = subtasks_count - 1;
          App.each(late_assignment_data['subtasks'], function(subtask_id, subtask_data) {
          	var not_assigned_class = user_id != subtask_data.assignee_id ? 'not_assigned' : '';
    				var common_subtask_wrapper = $('<div class="assignment subtask ' + not_assigned_class + '" assignee_id="' + subtask_data.assignee_id + '" subtask_id="' + subtask_id + '" due_on_timestamp="' + subtask_data.due_on.timestamp + '" task_id="' + late_assignment_data.task_id + '" project_id="' + late_assignment_data.project_id + '"></div>');
    				var common_subtask = common_subtask_wrapper.append(render_subtask_row(late_assignment_data, subtask_data));
    				
    				var calculated_assignments_length = existing_late_assignments_count ? common_subtask_order + existing_late_assignments_count : common_subtask_order;

            if(height_diff > 0) {
              common_subtask_wrapper.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height - height_diff);
            } else {
              common_subtask_wrapper.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height);
            } // if
    				
    				late_wrapper.append(common_subtask);
    				
    				common_subtask_order--;
        	});
		    }; // share_common_subtasks
		    
		    /**
		     * Cut subtasks to new user column
		     *
		     * @param Object new_assignments_wrapper
		     * @param Object existing_assignments
		     * @param Object orig_subtasks
		     * @param Integer new_assignee_id
		     * @return null
		     */
		    var cut_orig_subtasks = function(new_assignments_wrapper, existing_assignments, orig_subtasks, new_assignee_id, shared_assignment_user_ids, height_diff) {
		    	if(orig_subtasks.length) {
      			var orig_assignment_subtask_order = orig_subtasks.length - 1;
      			App.each(orig_subtasks, function(k, orig_assignment_subtask) {
      				var clone = $(orig_assignment_subtask).clone();
      				var clone_assignee_id = clone.attr('assignee_id');
      				
      				if(clone_assignee_id == new_assignee_id) {
      					clone.removeClass('not_assigned');
      				} else {
      					clone.addClass('not_assigned');
                shared_assignment_user_ids.push({ 'id' : clone_assignee_id });
      				} // if
      				
      				var calculated_assignments_length = existing_assignments.length ? orig_assignment_subtask_order + existing_assignments.length : orig_assignment_subtask_order;

              if(height_diff > 0) {
                clone.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height - height_diff);
              } else {
                clone.css('bottom', constants.due_late_margin_bottom + calculated_assignments_length * constants.assignment_height);
              } // if

        			new_assignments_wrapper.prepend(clone);
        			
        			$(orig_assignment_subtask).remove();
        			orig_assignment_subtask_order--;
      			});
      		} // if
		    }; // cut_orig_subtasks
		    
		    /**
		     * Recalculate tracked vs. estimated time values
		     *
		     * @param Object assignment
		     * @param Object wrapper
		     * @param boolean concat
		     */
		    var recalculate_tracked_vs_estimated_values = function(assignment, wrapper, concat) {
		    	var tracked_time = wrapper.find('.tracked_time');
					var estimated_time = wrapper.find('.estimated_time');
					
					var assignment_tracked_time = assignment.find('.tracked_vs_estimated_time .left_half');
					var assignment_estimated_time = assignment.find('.tracked_vs_estimated_time .right_half');
					
					if(concat) {
						var new_tracked_time = Number(tracked_time.html()) + Number(assignment_tracked_time.html());
						var new_estimated_time = Number(estimated_time.html()) + Number(assignment_estimated_time.html());
					} else {
						var new_tracked_time = Number(tracked_time.html()) - Number(assignment_tracked_time.html());
						var new_estimated_time = Number(estimated_time.html()) - Number(assignment_estimated_time.html());
					} // if

					tracked_time.html(cut_decimals_off(App.customNumberFormat(new_tracked_time, 2, '.', '.')));
					estimated_time.html(cut_decimals_off(App.customNumberFormat(new_estimated_time, 2, '.', '.')));
		    	
		      var re_class;
	      	
	      	if(!new_estimated_time) {
				    re_class = 'ok';
				  } else if(new_estimated_time && (new_estimated_time <= new_tracked_time)) {
				    re_class = 'underestimated';
				  } else {
				  	var round = 10;
				  	var percentage = (new_tracked_time / new_estimated_time) * 100;
				  	var rounded = Math.ceil(percentage / round) * round;
				  	
				  	if(rounded < 90) {
				  		re_class = 'ok';
				  	} else if(rounded >= 90) {
				  		re_class = 'close_to_estimated_time';
				  	} // if
				  } // if
				  
				  wrapper.attr('class', 'estimated_vs_tracked_time ' + re_class);
		    }; // recalculate_tracked_vs_estimated_values
	      
	      App.each(response, function(k, workload) {
	      	var time_span = typeof(workload['timespan']) == 'string' ? workload['timespan'] : '--';
	      	var offset = typeof(workload['offset']) == 'number' ? workload['offset'] : 0;
	    		
	    		var workload_wrapper = $('<div class="filter_results_head">' +
		          '<div class="time_span">' +
		          	'<a class="time_span_previous" offset="' + (offset - 1) + '" href="#"><<</a><span>' + App.clean(time_span) + '</span><a class="time_span_next" offset="' + (offset + 1) + '" href="#">>></a>' +
		          '</div>' +
	          '</div>' +
	          '<div class="filter_results_body">' +
	          	'<div class="items_wrapper"></div>' +
	          '</div>').appendTo(results_wrapper);
	          
	        $('.time_span').delegate('a', 'click', function() {
			      var link = $(this);
			      var new_offset = link.attr('offset');
			      
			      update_report(new_offset);
			    });
			    
			    render_workload(workload_wrapper, workload);
			    
			    /**
			     * Update report
			     * 
			     * @param integer offset
			     * @return boolean
			     */
			    var update_report = function(offset) {
			    	var form = $('#workload_report form');
			      var form_head = form.find('div.filter_criteria_head');
	          var url = form.attr('action');
	          var form_data = form.serialize();
	          
			      // Make sure to submit all form data no matter if they're disabled or not
	          form_head.find('input[disabled]').each(function() {
	          	form_data = form_data + '&' + $(this).attr('name') + '=' + $(this).val();
	          });
	          
	          url += url.indexOf('?') === -1 ? '?' + form_data : '&' + form_data;
	          
	          block_form(workload_wrapper, form_head);
			      
	          $.ajax({
	            'type': 'get', 
	            'url': url,
	            'data': { 'filter[offset]': offset },
	            'success': function(response) {
	            	unblock_form(workload_wrapper, form_head);
	            	
	            	if(typeof(response) == 'object' && response && response.length > 0) {
	            		workload = response[0]['__v'];
			          	
			          	update_timespan(workload);
			            render_workload(workload_wrapper, workload);
	            	} else {
	                workload_wrapper.append('<p class="empty_page">' + App.lang('Filter returned an empty result') + '</p>');
	              } // if
	            }, 
	            'error': function() {
	            	App.Wireframe.Flash.error('Failed to execute filter. Please try again later');
	            }
	          });
			    }; // update_report
			    
			    /**
			     * Update timeframe
			     *
			     * @param Object workload
			     * @return void
			     */
			    var update_timespan = function(workload) {
			    	time_span = typeof(workload['timespan']) == 'string' ? workload['timespan'] : '--';
			    	
			      workload_wrapper.find('a.time_span_previous').attr('offset', parseInt(workload['offset']) - 1);
            workload_wrapper.find('.time_span span').html(time_span);
            workload_wrapper.find('a.time_span_next').attr('offset', parseInt(workload['offset']) + 1);
			    }; // update_timespan
			    
			    /**
			     * Update assignment label and name (if needed)
			     * 
			     * @param Object assignment
			     * @return void
			     */
			    var update_assignment = function(assignment) {
			    	var current_assignment = wrapper.find('div[' + assignment.verbose_type_lowercase + '_id=' + assignment.task_id + ']');
				    var name_link = current_assignment.find('.assignment_name a');
				    var label = current_assignment.find('.label_tag');
				    
				    if(typeof(name_link) == 'object' && name_link.length !== 0 && name_link.text() != assignment.name) {
				    	name_link.contents()[0].textContent = App.excerpt(App.clean(assignment.name), 34); // make sure to preserve quick view indicator within current assignment link
				    } // if
				    
				    if(assignment.label != null && label.attr('title') != assignment.label.name) {
				    	label.attr('title', assignment.label.name);
				    	label.find('.label_background').attr('style', 'background-color: ' + assignment.label.bg_color + '');
				    } // if
				    
				    if(assignment.assignee == null || current_assignment.attr('assignee_id') != undefined && current_assignment.attr('assignee_id') != assignment.assignee.id) {
				    	update_report(workload.offset);
				    } // if
				    
				    if(current_assignment.attr('due_on_timestamp') != undefined && current_assignment.attr('due_on_timestamp') != assignment.due_on.timestamp) {
				    	update_report(workload.offset);
				    } // if
				    
				    if(assignment.is_archived) {
				    	update_report(workload.offset);
				    } // if
				    
				    if(assignment.is_completed) {
				    	update_report(workload.offset);
				    } // if
			    }; // update_assignment
			    
			    /**
			     * Delete assignment
			     */
			    var delete_assignment = function() {
			    	update_report(workload.offset);
			    }; // delete_assignment
			  	
			  	// Task updated
				  App.Wireframe.Events.bind('task_updated.content', function(event, task) {
				  	update_assignment(task);
				  });
				  
				  // Task deleted
				  App.Wireframe.Events.bind('task_deleted.content', function(event, task) {
				    delete_assignment();
				  });
				  
				  // Subtask updated
				  App.Wireframe.Events.bind('subtask_updated.item', function(event, subtask) {
				    update_assignment(subtask);
				  });
				  
				  // Subtask deleted
				  App.Wireframe.Events.bind('subtask_deleted.item', function(event, subtask) {
				    delete_assignment();
				  });

          App.Wireframe.PageTitle.clearActions();
	      });
	    },
	    'data' : {
	    	'companies' : {$companies|map nofilter},
	      'users' : {$users|json nofilter},
	      'projects' : {$projects|map nofilter},
	      'active_projects' : {$active_projects|map nofilter},
	      'project_users' : {$project_users|json nofilter},
	      'project_slugs' : {$project_slugs|json nofilter},
	      'project_categories' : {$project_categories|map nofilter},
		    'reassign_url' : {$reassign_url|json nofilter},
		    'task_url' : {$task_url|json nofilter},
		    'task_subtask_url' : {$task_subtask_url|json nofilter},
		    'todo_url' : {$todo_url|json nofilter},
		    'todo_subtask_url' : {$todo_subtask_url|json nofilter},
		    'labels' : {$labels|json nofilter}
	    }
	  });
  });
</script>