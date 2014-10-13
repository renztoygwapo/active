<?php

  /**
   * Render select filter type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_incoming_mail_due_on($params, &$smarty) {
    $due_ons = array(
      IncomingMailFilter::DUE_ON_EMPTY,
      IncomingMailFilter::DUE_ON_MESSAGE_RECEIVED,
      IncomingMailFilter::DUE_ON_NEXT_BUSSINESS_DAY
     );
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($due_ons as $due_on) {
      $option_attributes = $due_on == $value ? array('selected' => true) : null;
      $options[] = option_tag($due_on, $due_on, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // smarty_function_select_incoming_mail_due_on
