<?php

  /**
   * select_priority helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Select priority control
   * 
   * Params:
   * 
   * - Commong SELECT attributes
   * - Value - Selected priority
   *
   * @param array $params
   * @return string
   */
  function smarty_function_select_priority($params, &$smarty) {
    $optional = array_var($params, 'optional', false, true);
    
    if($optional) {
      $optional_text = array_var($params, 'optional_text', null, true);
      
      $value = array_var($params, 'value', PRIORITY_NORMAL, true);
      
      if(is_numeric($value)) {
        $value = (integer) $value;
      } // if
    } else {
      $value = (integer) array_var($params, 'value', PRIORITY_NORMAL, true);
    } // if
    
    if(is_int($value) && ($value > PRIORITY_HIGHEST || $value < PRIORITY_LOWEST)) {
      $value = PRIORITY_NORMAL;
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_priority');
    } // if
    
    $possibilities = array(
      PRIORITY_HIGHEST => lang('Highest'),
      PRIORITY_HIGH => lang('High'),
      PRIORITY_NORMAL => lang('Normal'),
      PRIORITY_LOW => lang('Low'),
      PRIORITY_LOWEST => lang('Lowest'),
    );
    
    if($optional) {
      return HTML::optionalSelectFromPossibilities($params['name'], $possibilities, $value, $params, $optional_text, '');
    } else {
      return HTML::selectFromPossibilities($params['name'], $possibilities, $value, $params);
    } // if
  } // smarty_function_select_priority