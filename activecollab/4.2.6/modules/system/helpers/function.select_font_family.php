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
  function smarty_function_select_font_family($params, &$smarty) {
    $name = array_var($params,'name',null,true);
    $value = array_var($params, 'value', null, true);
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Font Family --'), '');
      $options[] = option_tag('', '');
    } // if
   
    $font_family = array(
      'Arial, Helvetica, sans-serif',
      'Georgia, serif',
      'Tahoma, Geneva, sans-serif',
      'Verdana, Geneva, sans-serif',
      'Lucida Grande, Verdana, Arial, Helvetica, sans-serif'
    );
    
    if(is_foreachable($font_family)) {
      
      
      foreach($font_family as $font) {
        $options[] = option_tag($font, $font, array(
            'class' => 'object_option', 
            'selected' => $value == $font, 
          ));
      } // foreach
    } // if
    
    return HTML::select($name, $options, $params);
  } // smarty_function_select_company