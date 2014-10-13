<?php

  /**
   * link button helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * display link in appearance of button
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_link_button($params, &$smarty) {
  	$id = array_var($params, 'id', NULL);
  	$button_class = array_var($params, 'class', null);
  	$icon_class = array_var($params, 'icon_class', null);
  	$label = array_var($params, 'label', lang('Button'));
  	$href = array_var($params, 'href', '#');
  	
  	$return = '<a href="' . clean($href) . '" class="link_button ' . clean($button_class) . '" id="' . clean($id) . '"><span class="inner">';

  	if ($icon_class) {
      $return.= '<span class="icon ' . $icon_class . '">';
    } // if
  	
		$return.= lang($label);
		
  	if ($icon_class) {
      $return.= '</span>';
    } // if

  	$return.= '</span></a>';
  	
  	return $return;
  } // smarty_function_object_visibility