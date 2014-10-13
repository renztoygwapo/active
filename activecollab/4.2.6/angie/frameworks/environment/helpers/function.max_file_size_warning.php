<?php

  /**
   * max_file_size_warning helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Display max file size warning
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_max_file_size_warning($params, &$smarty) {
    static $max_file_size = false;
    
    AngieApplication::useHelper('filesize', GLOBALIZATION_FRAMEWORK, 'modifier');
    
    if($max_file_size === false) {
      $max_file_size = get_max_upload_size();
    } // if
    
    if(array_var($params, 'per_file')) {
      return lang('Max file size that you can upload is :size per file', array('size' => smarty_modifier_filesize($max_file_size)));
    } else {
      return lang('Max file size that you can upload is :size', array('size' => smarty_modifier_filesize($max_file_size))); 
    } // if
  } // smarty_function_max_file_size_warning