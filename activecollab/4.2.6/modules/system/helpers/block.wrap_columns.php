<?php

  /**
   * Wrap content columns
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap_columns($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $class = array_var($params, 'class', null);
    
    $return= '<div id="page_columns_wrapper" class="'.$class.'">' . $content . '</div>';
    $return.= '<script type="text/javascript">App.layout.evenPageColumns()</script>';
    
    return $return;
  } // smarty_block_form