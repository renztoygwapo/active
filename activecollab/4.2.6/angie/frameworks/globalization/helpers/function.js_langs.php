<?php

  /**
   * js_langs helper implementation
   * 
   * @package angie.frameworks.globalization
   * @subpackage helpers
   */

  /**
   * Print JS langs if locale is set
   * 
   * Parameters:
   * 
   * - locale - helper will print needed javascript language translations for this
   * locale
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_js_langs($params, &$smarty) {
    $locale = array_var($params, 'locale', null);
  	$language = Languages::findByLocale($locale);
    
    return '<script type="text/javascript">App.langs = ' . JSON::encode(
      $language instanceof Language ? $language->getTranslation(Language::DICTIONARY_CLIENTSIDE) : array()
    ) . '</script>';
  } // smarty_function_js_langs