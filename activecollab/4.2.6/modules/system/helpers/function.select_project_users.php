<?php

  /**
   * select_project_users helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select project users widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_users($params, &$smarty) {
    $project = array_var($params, 'project', null, true);
    
    if($project instanceof Project) {
      require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_users.php';
      
      $params['object'] = $project;
      
      return smarty_function_select_users($params, $smarty);
    } else {
      throw new InvalidInstanceError('project', $project, 'Project');
    } // if
  } // smarty_function_select_project_users