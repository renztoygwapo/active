<?php

  /**
   * select_mailbox_security helper implementation
   * 
   * @package angie.frameworks.email
   * @subpackage helpers
   */

  /**
   * Render select Mailbox type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_mailbox_security($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    $params['inline'] = true;
    
    return HTML::radioGroupFromPossibilities($name, array(
      MM_SECURITY_NONE => lang('None'), 
      MM_SECURITY_SSL => 'SSL', 
      MM_SECURITY_TLS => 'TLS'
    ), $value, $params);
  } // smarty_function_select_mailbox_security