<?php

  /**
   * Render select IM type control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_im_type($params, &$smarty) {
  	$name = array_var($params, 'name', null, true);
    $im_types = array('AIM', 'ICQ', 'MSN', 'Yahoo!', 'Jabber', 'Skype', 'Google');
    
    $value = null;
    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if
    
    $options = array();
    foreach($im_types as $im_type) {
      $options[] = HTML::optionForSelect($im_type, $im_type, $im_type == $value, array(
      	'class' => 'object_option'
      ));
    } // foreach
    
    return HTML::select($name, $options, $params);
  } // smarty_function_select_im_type

?>