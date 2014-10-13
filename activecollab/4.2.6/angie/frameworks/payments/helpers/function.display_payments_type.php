<?php

  /**
   * smarty_function_display_payments_type helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render display payments type
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_display_payments_type($params, &$smarty) {
    $value = array_required_var($params, 'value', true);
      
      $possibilities = array(
        Payment::USE_SYSTEM_DEFAULT => lang('Use system default'),
        Payment::DO_NOT_ALLOW => lang('Do not allow payments'), 
        Payment::ALLOW_FULL => lang('Allow only full payments'),
        Payment::ALLOW_PARTIAL =>  lang('Allow full and partial payments')
      );
       
      
      return $possibilities[$value];
  } // smarty_function_display_payments_type