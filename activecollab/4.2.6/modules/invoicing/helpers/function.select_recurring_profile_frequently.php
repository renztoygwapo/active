<?php

  /**
   * select_recurring_profile_frequently helper
   *
   * @package application.modules.invoicing
   * @subpackage helpers
   */
  
  /**
   * Render select_recurring_profile_frequently control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_recurring_profile_frequently($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value');
    
    $possibilities = array(
      RecurringProfile::FREQUENCY_DAILY => lang('Daily'),
      RecurringProfile::FREQUENCY_WEEKLY => lang('Weekly'),
      RecurringProfile::FREQUENCY_TWO_WEEKS => lang('Biweekly'),
      RecurringProfile::FREQUENCY_MONTHLY => lang('Monthly'),
      RecurringProfile::FREQUENCY_TWO_MONTHS => lang('Bimonthly'),
      RecurringProfile::FREQUENCY_THREE_MONTHS => lang('Every 3 Months'),
      RecurringProfile::FREQUENCY_SIX_MONTHS => lang('Every 6 Months'),
      RecurringProfile::FREQUENCY_YEARLY => lang('Yearly'),
      RecurringProfile::FREQUENCY_BIANNUAL => lang('Biannual'),
    );
    
    return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  } // smarty_function_select_payments_type