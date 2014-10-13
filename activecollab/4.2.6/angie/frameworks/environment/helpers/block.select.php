<?php

  /**
   * Select
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * select box
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_select($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
  	return HTML::select($params['name'], $content, $params);
  } // smarty_block_select