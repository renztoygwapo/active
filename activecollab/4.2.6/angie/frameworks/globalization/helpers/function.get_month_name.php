<?php

  /**
   * smarty_function_get_month_name helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Print month name for a given integer
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_get_month_name($params, &$smarty) {
    $month = array_var($params,'month');
    $short_name = array_var($params,'short_name');
    $language = array_var($params,'language',null);
    
    if($short_name) {
      $month_names = Globalization::getShortMonthNames($language);
    } else {
      $month_names = Globalization::getMonthNames($language);
    }//if
    
    return $month_names[$month];
    
  } // smarty_function_get_month_name