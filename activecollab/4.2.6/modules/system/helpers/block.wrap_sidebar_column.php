<?php

  /**
   * Wrap sidebar column
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap_sidebar_column($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    return '<div id="page_sidebar_column"><div id="page_sidebar_column_inner">' . $content . '</div></div>';
  } // smarty_block_form