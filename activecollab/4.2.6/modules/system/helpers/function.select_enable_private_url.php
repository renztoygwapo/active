<?php

  /**
   * Select role helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select role helper
   * 
   * Params:
   * 
   * - name - Select name attribute
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - active_user - Set if we are changing role of existing user so we can 
   *   handle situations when administrator role is displayed or changed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_enable_private_url($params, &$smarty) {
    $name = array_var($params, 'name', null, true);
    $arrValue = array(array('label'=>'Yes','value'=>'true'),array('label'=>'No','value'=>'false'));

    $value = array_var($params, 'value', null, true);

    if(isset($params['value'])) {
      $value = $params['value'];
      unset($params['value']);
    } // if

    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang(''), '');
    } // if
    foreach ($arrValue as $val) {
      $options[] = option_tag($val['label'], $val['value'],array(
            'selected' => $value == $val['value'], 
            ));
    }
    return HTML::select($name, $options, $params);
   
  } // smarty_function_select_enable_private_url(