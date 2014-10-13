<?php

  /**
   * Wrap editor field
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap_editor($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    require_once ENVIRONMENT_FRAMEWORK_PATH . '/helpers/block.wrap.php';
    
    if(isset($params['class']) && $params['class']) {
      $params['class'] .= ' big_editor';
    } else {
      $params['class'] = 'big_editor';
    } // if

    if(array_var($params, 'visual', true, true)) {
      $params['class'] .= ' visual';
    } else {
      $params['class'] .= ' not_visual';
    } // if
    
    return smarty_block_wrap($params, $content, $smarty, $repeat);  
  } // smarty_block_wrap_editor