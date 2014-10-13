<?php

  /**
   * Label helper implementation
   * 
   * @package angie.library.smarty
   */

  /**
   * Render label
   * 
   * Paramteres:
   * 
   * - after_text - text that will be put after label text. Default is ''
   * - required - puts a star after label text if this field is required
   *
   * @param array $params
   * @param string $connect
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_label($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $for = array_var($params, 'for', null, true);
    $after_text = array_var($params, 'after_text', ':', true);
    $is_required = array_var($params, 'required', false, true);
    
    $text = array_var($params, 'not_lang') ? $content : lang($content, $params);
    
    $main_label = array_var($params, 'main_label', true);
    $class_extension = $main_label ? 'main_label' : 'secondary_label';
    
    if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE) {
      $class_extension .= " ui-input-text";
   	} // if

    if(isset($params['class'])) {
      $params['class'] .= " $class_extension";
    } else {
      $params['class'] = $class_extension;
    } // if
    
    return HTML::label($text, $for, $is_required, $params, $after_text);
  } // smarty_block_label