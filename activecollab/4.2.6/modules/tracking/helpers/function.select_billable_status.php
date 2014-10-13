<?php

  /**
   * select_billable_status helper implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage helpers
   */

  /**
   * Render select billable status field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_billable_status($params, &$smarty) {
  	$possibilities = array(
      BILLABLE_STATUS_NOT_BILLABLE => lang('Not Billable'),
      BILLABLE_STATUS_BILLABLE => lang('Billable'),
      BILLABLE_STATUS_PENDING_PAYMENT => lang('Pending Payment'),
      BILLABLE_STATUS_PAID => lang('Paid'),
    );
    
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  } // smarty_function_select_billable_status