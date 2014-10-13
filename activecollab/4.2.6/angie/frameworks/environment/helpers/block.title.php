<?php

  /**
   * Set page title to $content value
   * 
   * Parameters:
   * 
   * - not_lang - Use raw value...
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_title($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    if(array_var($params, 'lang', true)) {
      $content = lang($content, $params, false); // Params will be cleaned by page construction
    } // if
    
    $smarty->getVariable('wireframe')->value->setPageTitle($content);
  } // smarty_block_title