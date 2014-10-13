<?php

  /**
   * select_mailbox_type helper implementation
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
  function smarty_function_select_mailbox_type($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    $params['inline'] = true;
    
    return HTML::radioGroupFromPossibilities($name, array(
      MM_SERVER_TYPE_POP3 => 'POP3', 
      MM_SERVER_TYPE_IMAP => 'IMAP', 
    ), $value, $params);
  } // smarty_function_select_mailbox_type