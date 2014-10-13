<?php

  /**
   * select_project_user helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select project user widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_user($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $params['object'] = $project;
    
    require_once AUTHENTICATION_FRAMEWORK_PATH . '/helpers/function.select_user.php';
    
    return smarty_function_select_user($params, $smarty);
  } // smarty_function_select_project_user