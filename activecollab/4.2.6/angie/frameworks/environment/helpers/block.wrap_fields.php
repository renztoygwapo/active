<?php

  /**
   * wrap_fields block implementation
   *
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Print wrapper div around fields
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string|null
   */
  function smarty_block_wrap_fields($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return null;
    } // if

    $properties = '';
    if(is_foreachable($params)) {
      foreach($params as $property => $value) {
        $properties .= " $property='$value'";
      }//foreach
    }//if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
      return '<div class="fields_wrapper" data-role="fieldcontain">' . "\n$content\n" . '</div>';
    } else {
      return '<div class="fields_wrapper" ' . $properties . '>' . "\n$content\n" . '</div>';
    } // if
  } // smarty_block_wrap_buttons