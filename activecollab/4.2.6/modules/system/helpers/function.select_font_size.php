<?php

  /**
   * select_company helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select company box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_font_size($params, &$smarty) {
    $name = array_var($params,'name',null,true);
    $value = array_var($params, 'value', null, true);
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Font Size --'), '');
      $options[] = option_tag('', '');
    } // if
   
    for($i=8;$i<=20;$i++) {
      $font_size[] = $i . 'px';
    }//for
    
    for($i=22;$i<=48;$i++) {
      $font_size[] = $i . 'px';
      $i++;
    }
    
    if(is_foreachable($font_size)) {
      foreach($font_size as $size) {
        $options[] = option_tag($size, $size, array(
            'class' => 'object_option', 
            'selected' => $value == $size, 
          ));
      } // foreach
    } // if
    
    return HTML::select($name, $options, $params);
  } // smarty_function_select_company