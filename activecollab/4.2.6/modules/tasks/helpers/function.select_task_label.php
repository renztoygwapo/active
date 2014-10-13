<?php

  /**
   * select_task_label helper implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage helpers
   */

  /**
   * Render select task label widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return unknown
   */
  function smarty_function_select_task_label($params, &$smarty) {
    require_once LABELS_FRAMEWORK_PATH . '/helpers/function.select_label.php';
    
    $params['type'] = 'AssignmentLabel';
    return smarty_function_select_label($params, $smarty);
  } // smarty_function_select_task_label