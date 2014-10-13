<?php

  /**
   * select_payment_status helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select status control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_payment_status($params, &$smarty) {
    $selected = array_var($params, 'selected', null, true);
    $items = array_var($params, 'items', null, true);
    
    if(!$items) {
      $items = array(
        Payment::STATUS_CANCELED,
        Payment::STATUS_DELETED,
        Payment::STATUS_PAID,
        Payment::STATUS_PENDING
      );
    } //if
    
    $options = array();
    if(is_foreachable($items)) {
      foreach($items as $item) {
       $attr =''; 
       if($item == $selected) {
          $attr = array('selected' => 'selected');
        } //if
        $options[] = option_tag(lang($item), $item, $attr);
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_payment_status

?>