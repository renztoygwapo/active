<?php

  /**
   * project_exporter_task_list helper
   *
   * @package activeCollab.modules.tasks
   * @subpackage helpers
   */
  
  /**
   * Shows a list of tasks
   *
   * Parameters:
   * 
   * - project - instance of Project
   * - category  -instance of Category
   * - milestone - instance of Milestone
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_task_list($params, $template) {
  	if (!((boolean) DB::executeFirstCell('SELECT COUNT(name) FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', TASKS_MODULE))) {
  		return '';
  	} //if
  	$project = array_var($params, 'project', null);
  	if (!($project instanceof Project)) {   
			throw new InvalidInstanceError('project', $project, 'Project');  		
  	} // if
  	
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$visibility = array_var($params, 'visibility', $template->tpl_vars['visibility']->value);
  	$category = array_var($params, 'category', null);
  	$per_query = array_var($params, 'per_query', 500);
  	$milestone = array_var($params, 'milestone', null);
    $navigation_sections = array_var($params, 'navigation_sections', null);

    $return = '<div id="milestone_tasks" class="object_info">';
  	
  	if ($milestone instanceof Milestone) {
  	  $tasks_count = Tasks::count(array("project_id = ? AND type = 'Task' AND milestone_id = ? AND state >= ? AND visibility >= ?", $project->getId(), $milestone->getId(), STATE_ARCHIVED, $visibility));
  	  if ($tasks_count) {
        $return .= '<h3>' . lang('Tasks') . '</h3>';
      } else {
        return '';
      } //if
  	} else {
  	  $tasks_count = Tasks::countByProject($project, $category, STATE_ARCHIVED, $visibility);
  	} //if
  	
  	if (!$tasks_count) {
  	  return (!$category) ? '<p>' . lang('There are no tasks on this project') . '</p>' : '<p>' . lang('There are no tasks in this category') . '</p>';  
  	} // if
  	
	$loops = ceil($tasks_count / $per_query);
	
	$current_loop = 0;
	$return .= '<table class="common" id="tasks_list">';
	while ($current_loop < $loops) {
		if ($category) {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Task' AND state >= ? AND visibility >= ? AND category_id = ? ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), STATE_ARCHIVED, $visibility, $category->getId());
		} elseif ($milestone instanceof Milestone) {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Task' AND milestone_id = ? AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), $milestone->getId(), STATE_ARCHIVED, $visibility);
		} else {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Task' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), STATE_ARCHIVED, $visibility);
		} // if
		
		if ($result instanceof DBResult) {
    	foreach ($result as $task) {
    		if (!$navigation_sections || ($navigation_sections && array_key_exists('tasks', $navigation_sections))) {
          $permalink = 'href="' . $template->tpl_vars['url_prefix']->value . 'tasks/task_' . $task['integer_field_1'] . '.html"';
        } else {
          //$permalink = 'href="javascript:void(0)" onclick="alert(\'' . lang('Task section is not implemented in this exported project') . '\')"';
          continue;
        } //if

				$return .= '<tr>';
				$return .= '<td class="column_id"><a ' . $permalink . '>' . $task['integer_field_1'] . '</a></td>';
				$return .= '<td class="column_name"><a ' . $permalink . '>' . clean($task['name']) . '</a></td>';
				$return .= '<td class="column_date">' . smarty_modifier_date($task['created_on']) . '</td>';                                 
				$return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $task['created_by_id'], 'name' => $task['created_by_name'], 'email' => $task['created_by_email']), $template) . '</td>';
			  $return .= '</tr>';
    	} //foreach
		} //if
		
		set_time_limit(30);
		$current_loop ++;
	} // while
	$return.= '</table></div>';
	
	return $return;
  } // smarty_function_project_exporter_task_list