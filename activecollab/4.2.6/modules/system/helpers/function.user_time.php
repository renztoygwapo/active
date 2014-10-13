<?php

  /**
   * user_time helper definition
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Show users local time based on his or hers local settings
   * 
   * Parameters:
   * 
   * - user - User, if NULL logged user will be used
   * - datetime - Datetime value that need to be displayed. If NULL request time 
   *   will be used
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_time($params, &$smarty) {
    $user = array_var($params, 'user');
    if(!($user instanceof User)) {
      $user = Authentication::getLoggedUser();
    } // if
    
    if(!($user instanceof User)) {
      return lang('Unknown time');
    } // if
    
    $value = array_var($params, 'datetime');
    if(!($value instanceof DateValue)) {
      $value = new DateTimeValue();
    } // if
    
    AngieApplication::useHelper('time', GLOBALIZATION_FRAMEWORK, 'modifier');
    return clean(smarty_modifier_time($value, get_user_gmt_offset($user)));
  } // smarty_function_user_time