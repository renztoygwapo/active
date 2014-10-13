<?php

  /**
   * select_payment_status_reason helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select status reason control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_payment_status_reason($params, &$smarty) {
    $selected = array_var($params, 'selected', null, true);
    $items = array_var($params, 'items', null, true);
    if(!$items) {
      $items = array(
        Payment::REASON_FRAUD,
        Payment::REASON_OTHER,
        Payment::REASON_REFUND
      );
    } //if
    $options = array();
    $options[] = option_tag(lang('--Select--'), '');
    if(is_foreachable($items)) {
      foreach($items as $item) {
        $attr = '';
        if($item == $selected) {
          $attr = array('selected' => 'selected');
        } //if
        $options[] = option_tag(lang($item), $item, $attr);
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // select_payment_status_reason

?>