<?php

  /**
   * select_project helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select project helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'IUser');
    $name = array_required_var($params, 'name', true);
    
    if(empty($params['class'])) {
      $params['class'] = 'select_project';
    } else {
      $params['class'] .= ' select_project';
    } // if
    
    $show_all = array_var($params, 'show_all', false) && ($user->isProjectManager() || $user->isFinancialManager());
    $value = array_var($params, 'value', null, true);
    $min_state = array_var($params, 'min_state', STATE_VISIBLE);
    
    $projects_table = TABLE_PREFIX . 'projects';
    $project_users_table = TABLE_PREFIX . 'project_users';
    
    if($show_all) {
      $projects = DB::execute("SELECT $projects_table.id, $projects_table.name, $projects_table.completed_on FROM $projects_table WHERE $projects_table.state >= ? ORDER BY $projects_table.name", $min_state);
    } else {
      $projects = DB::execute("SELECT $projects_table.id, $projects_table.name, $projects_table.completed_on FROM $projects_table, $project_users_table WHERE $project_users_table.user_id = ? AND $project_users_table.project_id = $projects_table.id AND $projects_table.state >= ? ORDER BY $projects_table.name", $user->getId(), $min_state);
    } // if
    
    $exclude = (array) array_var($params, 'exclude', array(), true);
    
    $active_options = array();
    $archived_options = array();
    
    if($projects) {
      foreach($projects as $k => $project) {
        if(in_array($project['id'], $exclude)) {
          continue;
        } // if
        
        if($project['completed_on']) {
          $archived_options[] = HTML::optionForSelect($project['name'], $project['id'], $project['id'] == $value);
        } else {
          $active_options[] = HTML::optionForSelect($project['name'], $project['id'], $project['id'] == $value);
        } // if
      } // if
    } else {
      return lang('No projects');
    } // if
    
    $optional = array_var($params, 'optional', false, true);
    $options = array();

    if(is_foreachable($active_options)) {
      $options[] = HTML::optionGroup(lang('Active'), $active_options, array('id' => 'opt_active'));
    } // if
    
    if(is_foreachable($active_options) && is_foreachable($archived_options)) {
      $options[] = HTML::optionForSelect('', '');
    } // if
    
    if(is_foreachable($archived_options)) {
      $options[] = HTML::optionGroup(lang('Completed'), $archived_options, array('id' => 'opt_completed'));
    } // if
    
    if($optional) {
      return HTML::optionalSelect($name, $options, $params, lang('Any Project'));
    } else {
      return HTML::select($name, $options, $params);
    } // if
  } // smarty_function_select_project