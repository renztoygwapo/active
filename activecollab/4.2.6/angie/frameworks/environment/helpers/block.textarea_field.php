<?php

  /**
   * Textarea helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render textarea
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
  */
  function smarty_block_textarea_field($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    if(!isset($params['rows'])) {
      $params['rows'] = 10;
    } // if
    
    if(!isset($params['cols'])) {
      $params['cols'] = 48;
    } // if
    
    return HTML::textarea(@$params['name'], $content, $params);
  }// smarty_block_textarea_field