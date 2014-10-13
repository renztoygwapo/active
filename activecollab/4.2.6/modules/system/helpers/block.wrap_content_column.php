<?php

  /**
   * Wrap content column
   * 
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap_content_column($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $return= '<div id="page_content_column"><div id="page_content_column_inner">' . $content . '</div></div>';
    return $return;
  } // smarty_block_form