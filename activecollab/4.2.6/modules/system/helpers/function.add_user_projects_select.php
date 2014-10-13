<?php

  /**
   * add_user_projects_select helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select projects from all clients box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_add_user_projects_select($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');

    $name = array_var($params, 'name', 'select_projects', true);
    $show_all = array_var($params, 'show_all', false, true);
    
    // We need to get all active projects
    $all_projects = Projects::find(array(
    	'conditions' => array("state >= ? AND completed_on IS NULL", STATE_VISIBLE),
      'order' => 'name'
    ));
    
    // User projects
    $user_projects = ($temp = Projects::findActiveByUser($user)) ? $temp->toArray() : array();

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_projects');
    } // if

    if(isset($params['class'])) {
      $params['class'] .= ' select_projects_inline';
    } else {
      $params['class'] = 'select_projects_inline';
    } // if

    $projects = array();

    if($all_projects && is_foreachable($all_projects)) {
      foreach($all_projects as $project) {
        if($show_all || !in_array($project, $user_projects)) {
          $projects[$project->getId()] = $project->getName();
        } // if
      } // foreach

      $options = array(
        'projects' => JSON::valueToMap($projects),
        'name' => $name
      );

      AngieApplication::useWidget('add_user_projects_select', SYSTEM_MODULE);
      return '<div id="' . $params['id'] . '"></div><script type="text/javascript">$(\'#' . $params['id'] . '\').addUserProjectsSelect(' . JSON::encode($options) . ')</script>';
    } // if

    return '';
  } // smarty_function_add_user_projects_select