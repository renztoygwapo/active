<?php

  /**
   * select_project_template helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select project template widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_project_template($params, &$smarty) {
    $value = array_var($params, 'value', null, true);

	  return HTML::optionalSelectFromPossibilities(@$params['name'], ProjectTemplates::getIdNameMap(), $value, $params, lang('-- Create a Blank Project --'));
  } // smarty_function_select_project_template