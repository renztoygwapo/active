<?php

  /**
   * project_exporter_milestone_list helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Shows a list of milestones
   *
   * Parameters:
   * 
   * - project - instance of Project
   * 
   * - completed - boolean parameter that determines weather to show active or completed milestones
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_milestone_list($params, $template) {
  	$project = array_required_var($params, 'project', true, 'Project');
  	
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$per_query = array_var($params, 'per_query', 500);
  	$completed = array_var($params, 'completed', false);
  	
  	$visibility = array_var($params, 'visibility', $template->tpl_vars['visibility']->value);
  	$milestones_count = Milestones::countByProject($project, STATE_ARCHIVED, $visibility);
  	
  	if (!$milestones_count) {
  		return '<p>' . lang('There are no milestones in this project') . '</p>';
  	} // if
  	
	  $loops = ceil($milestones_count / $per_query);
	
	  $current_loop = 0;
	  $return = '<table class="common" id="milestones_list">';
	  while ($current_loop < $loops) {
		
	    $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Milestone' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), STATE_ARCHIVED, $visibility);
		
      $resource = $result->getResource();
			while ($milestone = mysql_fetch_assoc($resource)) {
			    $class_milestone = new Milestone();
			    $class_milestone->loadFromRow($milestone);
			    if ($completed === $class_milestone->complete()->isCompleted()) {
	  			  $permalink = $template->tpl_vars['url_prefix']->value . 'milestones/milestone_' . $milestone['id'] . '.html';
	  			  $return .= '<tr>';
	  			  $return .= '<td class="column_id"><a href="' . $permalink . '">' . $milestone['id'] . '</a></td>';
	  			  $return .= '<td class="column_name"><a href="' . $permalink . '">' . clean($milestone['name']) . '</a></td>'; 
	  			  $return .= '<td class="column_date">' . smarty_modifier_date($milestone['created_on']) . '</td>';                                 
	  			  $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $milestone['created_by_id'], 'name' => $milestone['created_by_name'], 'email' => $milestone['created_by_email']), $template) . '</td>';
	  		      $return .= '</tr>';
			    } //if
			} // while
			
			set_time_limit(30);
			$current_loop ++;
		} // while
		$return.= '</table>';
		mysql_free_result($resource);
	
		return $return;
  } // smarty_function_project_exporter_milestone_list