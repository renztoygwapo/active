<?php

  /**
   * Objects label helper implementation
   * 
   * @package angie.frameworks.labels
   * @subpackage helpers
   */

  /**
   * Render object's label
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_object_label($params, &$smarty) {
    $object = array_required_var($params, 'object', false, 'ILabel');
    $short_label = (boolean) isset($params['short']) ? $params['short'] : false;
    
    if($object->label()->get() instanceof Label) {
      return $object->label()->get()->render($short_label);
    } else {
      return '';
    } // if
  } // smarty_function_object_label