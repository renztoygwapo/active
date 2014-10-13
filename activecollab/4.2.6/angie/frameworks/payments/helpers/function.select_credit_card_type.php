<?php

  /**
   * select_credit_card_type helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select credit card type control
   * 
   * Params:
   * 
   * - cc_types - available cc types - array
   * 
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_credit_card_type($params, &$smarty) {
    $cc_types = array_var($params, 'cc_types', null, true);
    if(!array($cc_types)) {
      throw new InvalidParamError('CC Types', $cc_types, '$cc_types value is expected to be an array ov available CC', true);
    } // if
    $options = array();
    if(is_foreachable($cc_types)) {
      $completed_options = array();
      foreach($cc_types as $cc_type) {
        if(is_foreachable($cc_type)) {
          $options[] = option_tag(lang($cc_type['name']), $cc_type['value'], $cc_type['attr']);
        }//if
      } // foreach
   } // if
    
    return select_box($options, $params);
  } // smarty_function_select_milestone

?>