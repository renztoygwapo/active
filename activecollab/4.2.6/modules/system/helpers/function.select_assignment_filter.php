<?php

  /**
   * select_assignment_filter helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render assignment filter picker
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_assignment_filter($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'User');
    $name = array_required_var($params, 'name');
    $value = array_var($params, 'value', null, true);
    
    if(array_var($params, 'optional', true, true)) {
      return HTML::optionalSelectFromPossibilities($name, DataFilters::getIdNameMap('AssignmentFilter', $user), $value, $params);
    } else {
      return HTML::selectFromPossibilities($name, DataFilters::getIdNameMap('AssignmentFilter', $user), $value, $params);
    } // if
  } // smarty_function_select_assignment_filter