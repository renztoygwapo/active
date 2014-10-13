<?php

  /**
   * Select language helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select language box
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_language($params, &$smarty) {
    $default_language_id = ConfigOptions::getValue('language');
    $name = array_required_var($params, 'name', true);
    $value = array_var($params, 'value', $default_language_id, true);
    $optional = array_var($params, 'optional', false, true);
    $languages = Languages::getIdNameMap();
    $preselect_default = array_var($params, 'preselect_default', false, true);

    if (!$value && $value !== 0 && $preselect_default) {
      $value = $default_language_id;
    } // if
    
    if($optional) {
      $default_language_name = $languages && isset($languages[$default_language_id]) ? $languages[$default_language_id] : lang('Built-in English');
      return HTML::optionalSelectFromPossibilities($name, $languages, $value, $params, lang('-- System Default (:value) --', array('value' => $default_language_name)), '');
    } else {
      return HTML::selectFromPossibilities($name, $languages, $value, $params);
    } // if
  } // smarty_function_select_language