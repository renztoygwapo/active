<?php

  /**
   * select_expense_category helper implementation
   * 
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Render select expense category box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_expense_category($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    if(empty($value)) {
      $value = ExpenseCategories::getDefaultId();
    } // if
    
    return HTML::selectFromPossibilities($name, ExpenseCategories::getIdNameMap(), $value, $params);
  } // smarty_function_select_expense_category