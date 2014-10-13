<?php
  /**
   * select_country helper
   *
   * @package angie.framework.payments
   * @subpackage helpers
   */
  
  /**
   * Render select country control
   * 
   * Params:
   * 
   * - countries - available countries - array
   * 
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_supported_country($params, &$smarty) {
    $countries = array_var($params, 'countries', null, true);

    if(!array($countries)) {
      throw new InvalidParamError('countries', $countries, '$countries value is expected to be an array ov available countries', true);
    } // if
    $value = array_var($params,'value', false, true);
    if(!$value) {
      $value = 'US';
    } //if

    $options = array();
    if(is_foreachable($countries)) {
      $completed_options = array();
      foreach($countries as $country) {

        if(is_foreachable($country)) {
          $options[] = HTML::optionForSelect(ucwords(strtolower_utf($country['name'])), $country['value'], $country['value'] == $value);
        }//if
      } // foreach
   } // if
    $name = array_required_var($params,'name',true);


    return HTML::select($name,$options,$params);
  } // smarty_function_select_milestone
