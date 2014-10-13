<?php

  /**
   * project_exporter_discussion_list helper
   *
   * @package activeCollab.modules.discussions
   * @subpackage helpers
   */
  
  /**
   * Shows a list of discussions
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
  
  function smarty_function_project_exporter_discussion_list($params, $template) {
  	if (!((boolean) DB::executeFirstCell('SELECT COUNT(name) FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', DISCUSSIONS_MODULE))) {
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

  	$return = '<div id="milestone_discussions" class="object_info">';
  	
  	if ($milestone instanceof Milestone) {
  	  $discussions_count = Discussions::count(array("project_id = ? AND type = 'Discussion' AND milestone_id = ? AND state >= ? AND visibility >= ?", $project->getId(), $milestone->getId(), STATE_ARCHIVED, $visibility));
  	  if ($discussions_count) {
        $return .= '<h3>' . lang('Discussions') . '</h3>';
      } else {
        return '';
      } //if
  	} else {
  	  $discussions_count = Discussions::countByProject($project, $category, STATE_ARCHIVED, $visibility);
  	} //if
  	
  	if (!$discussions_count) {
  	  return (!$category) ? '<p>' . lang('There are no discussions on this project') . '</p>' : '<p>' . lang('There are no discussions in this category') . '</p>';  
  	} // if
  	
	$loops = ceil($discussions_count / $per_query);
	
	$current_loop = 0;
	$return .= '<table class="common" id="discussions_list">';
	while ($current_loop < $loops) {
		if ($category) {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Discussion' AND state >= ? AND visibility >= ? AND category_id = ? ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), STATE_ARCHIVED, $visibility, $category->getId());
		} elseif ($milestone instanceof Milestone) {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Discussion' AND milestone_id = ? AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), $milestone->getId(), STATE_ARCHIVED, $visibility);
		} else {
		  $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE project_id = ? AND type = 'Discussion' AND state >= ? AND visibility >= ?  ORDER BY ISNULL(due_on), due_on LIMIT " . $current_loop * $per_query . ", $per_query", $project->getId(), STATE_ARCHIVED, $visibility);
		} // if
		
		if ($result instanceof DBResult) {
			foreach ($result as $discussion) {
        if (!$navigation_sections || ($navigation_sections && array_key_exists('discussions', $navigation_sections))) {
				  $permalink = 'href="' . $template->tpl_vars['url_prefix']->value . 'discussions/discussion_' . $discussion['id'] . '.html"';
        } else {
          continue;
        } //if
					
				$return .= '<tr>';
				$return .= '<td class="column_id"><a ' . $permalink . '>' . $discussion['id'] . '</a></td>';
				$return .= '<td class="column_name"><a ' . $permalink . '>' . clean($discussion['name']) . '</a></td>';
				$return .= '<td class="column_date">' . smarty_modifier_date($discussion['created_on']) . '</td>';                                 
				$return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $discussion['created_by_id'], 'name' => $discussion['created_by_name'], 'email' => $discussion['created_by_email']), $template) . '</td>';
			  $return .= '</tr>';
			} // foreach
		} //if
		
		set_time_limit(30);
		$current_loop ++;
	} // while
	$return.= '</table></div>';
	
	return $return;
  } // smarty_function_project_exporter_discussion_list