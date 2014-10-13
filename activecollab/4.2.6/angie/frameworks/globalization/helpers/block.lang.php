<?php

  /**
   * lang helper implementation
   *
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Return lang for a given code text and parameters
   * 
   * Paramteres:
   * 
   * - clean_params - boolean - Clean params before they are inserted in string, 
   *   true by default
   * - language - Language - Force translation it his language
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_lang($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return false;
    } // if
    
    $clean_params = isset($params['clean_params']) ? (boolean) $params['clean_params'] : true; // true by default

    $language = null;
    if (isset($params['language'])) {
      $language = $params['language'];
      unset($params['language']);
    } // if

    
    return Globalization::lang($content, $params, $clean_params, $language);
  } // smarty_block_lang