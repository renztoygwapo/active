<?php

  /**
   * select_estimate helper implementation
   *
   * @package angie.frameworks.estimates
   * @subpackage helpers
   */

  /**
   * Select estimated time value
   *
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_select_estimate($params, &$smarty) {
    if(AngieApplication::isModuleLoaded('tracking')) {
      $settings = array(
        'name' => array_required_var($params, 'name', true), 
        'value' => (float) array_var($params, 'value', 0, true), 
        'optional' => (boolean) array_var($params, 'optional', true, true), 
        'short' => isset($params['short']) && $params['short'], 
      );
      
      $id = isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('select_estimate');

      AngieApplication::useWidget('select_estimate', TRACKING_MODULE);
      return '<span id="' . $id . '"></span><script type="text/javascript">$("#' . $id . '").selectEstimate(' . JSON::encode($settings) . ');</script>';
    } // if
  } // smarty_function_select_estimate