<?php

  /**
   * select_code_syntax helper implementation
   *
   * @package angie.frameworks.visual_editor
   * @subpackage helpers
   */

  /**
   * Render select code syntax field
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_code_syntax($params, &$smarty) {
  	$possibilities = array();
  	$supported_syntaxes = HyperlightForAngie::getAvailableLanguages();
  	if (is_foreachable($supported_syntaxes)) {
  		foreach ($supported_syntaxes as $supported_syntax => $supported_languages) {
  			$possibilities[$supported_syntax] = implode(', ', $supported_languages);
  		} // foreach
  	} // if
  	
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', null, true);
    
    return HTML::selectFromPossibilities($name, $possibilities, $value, $params);
  } // smarty_function_select_code_syntax