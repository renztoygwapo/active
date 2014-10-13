<?php

  /**
   * Image tag helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Generate image tag based on properties (same as for image_url)
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_image($params, &$smarty) {
    $name = array_required_var($params, 'name', true);
    $module = array_var($params, 'module', DEFAULT_MODULE, true);
    $interface = array_var($params, 'interface', null, true);
    
    $params['src'] = AngieApplication::getImageUrl($name, $module, $interface);

    $modifiers = array_var($params, 'modifiers', null, true);

    if($modifiers) {
      $modifiers = explode(',', $modifiers);

      $extension_pos = strrpos($name, '.');

      $srcset = array();

      foreach($modifiers as $modifier) {
        $srcset[] = AngieApplication::getImageUrl(substr($name, 0, $extension_pos + 1) . $modifier . substr($name, $extension_pos), $module, $interface) . " $modifier";
      } // foreach

      $params['srcset'] = implode(', ', $srcset);
    } // if
    
    if(!isset($params['alt'])) {
      $params['alt'] = '';
    } // if
    
    return HTML::openTag('img', $params);
  } // smarty_function_image