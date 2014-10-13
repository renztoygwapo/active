<?php

  /**
   * Print wrapper div around buttons
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_wrap_buttons($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    $properties = '';
    
    if(is_foreachable($params)) {
      foreach($params as $property => $value) {
        $properties .= " $property='$value'";
      }//foreach
    }//if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
      return '<div class="button_holder" data-role="fieldcontain">' . "\n$content\n" . '</div>';
    } else {
      return '<div class="button_holder" ' . $properties . '>' . "\n$content\n" . '</div>';
    } // if
  } // smarty_block_wrap_buttons