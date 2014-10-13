<?php

  /**
   * add_bread_crumb helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Add bread crumb
   * 
   * Parameters:
   * 
   * - url - crumb URL, optional
   * - not_lang - use raw value, optional
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_add_bread_crumb($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $url = array_var($params, 'url', null, true);
    $name = array_var($params, 'name', 'final', true);
    $not_lang = (boolean) array_var($params, 'not_lang', false, true);
    $text = $not_lang ? $content : lang($content, $params);
    
    if($text) {
      $smarty->getVariable('wireframe')->value->breadcrumbs->add($name, $text, $url);
    } // if
    
    return '';
  } // smarty_block_add_bread_crumb