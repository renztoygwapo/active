<?php

  /**
   * select_completion_status helper
   *
   * @package angie.framework.complete
   * @subpackage helpers
   */
  
  /**
   * Render select completion status control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_completion_status($params, &$smarty) {
  	$value = array_var($params, 'value', 0, true);
  	$name = array_var($params, 'name', null, true);
  	
  	$label_type = strtolower(array_var($params, 'label_type', null, true));
  	if ($label_type == 'inner') {
  		$label = array_var($params, 'label', null, true);
  	} else {
  		$label = null;
  	} // if

    $value = $value ? '1' : '0';
    
    $items = array(
      '0' => lang('Open'),
    	'1' => lang('Completed')
		);
		   
    $options = array();
    if(is_foreachable($items)) {
      foreach($items as $item_value => $item_label) {
       $attr ='';
       if($item_value == $value) {
          $attr = array('selected' => 'selected');
        } //if
        $options[] = option_tag($item_label, $item_value, $attr);
      } // foreach
    } // if
    
		if ($label) {
			return HTML::select($name, option_group_tag($label, $options), $params);
		} else {
			return HTML::select($name, $options, $params);	
		} // if
    
  } // smarty_function_select_completion_status

?>