<?php

  /**
   * select_project_label helper implementation
   *
   * @package activeCollab.modules.project
   * @subpackage helpers
   */

  /**
   * Render select project label widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return unknown
   */
  function smarty_function_select_project_label($params, &$smarty) {
    require_once LABELS_FRAMEWORK_PATH . '/helpers/function.select_label.php';
    
    $params['type'] = 'ProjectLabel';
    return smarty_function_select_label($params, $smarty);
  } // smarty_function_select_project_label