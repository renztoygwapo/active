<?php

  /**
   * Render select font
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_font($params, &$smarty) {
    $value = trim(array_var($params, 'value'));
    if (!$value) {
    	$value = 'dejavusans';
    } // if
    
    require_once(ANGIE_PATH .'/classes/tcpdf/init.php');
    
    $system_fonts = (array) get_files(TCPDF_FONTS_PATH, array('php')); 
    $custom_fonts = (array) get_files(CUSTOM_PATH . '/fonts', array('php'));
    $fonts = array_merge($custom_fonts, $system_fonts);
            
    $options = array();
    foreach ($fonts as $font) {
      $pathinfo = pathinfo($font);
      $font_name = $pathinfo['filename'];

      if (strpos($font_name, 'uni2cid') === 0 || strpos($font_name, 'cid0') === 0) {
        continue;
      } // if

      $option_attributes = array();
      if ($font_name == $value) {
        $option_attributes['selected'] = true;
      } // if
      $options[] = option_tag($font_name, $font_name, $option_attributes);
    } // foreach
    
    return select_box($options, $params);
  } // select_font