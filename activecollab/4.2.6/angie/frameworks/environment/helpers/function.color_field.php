<?php

  /**
   * color_field helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render color field input
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_color_field($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $value = isset($params['value']) && $params['value'] ? $params['value'] : null;
    
    $picker_id = array_var($params, 'id', HTML::uniqueId('color_picker'));
        
		$color = array_var($params, 'value', null, true);
		if (!$color) {
			$color = array_var($params, 'default_color', '#000000');
		} else if (substr($color, 0, 1) != '#') {
			$color = '#' . $color;
		} // if
		$params['value'] = $color;

		$class = array_var($params, 'class', '');
		
		$return = '<div class="color_field ' . $class . '" id="' . $picker_id . '">';
		
		$label = isset($params['label']) && $params['label'] ? $params['label'] : null;
    if($label) {
      unset($params['label']);
      
      $return .= HTML::label($label, $picker_id, (isset($params['required']) && $params['required']), array('class' => 'main_label'));
    } // if

		$return .= '</div>';
    AngieApplication::useWidget('color_field', ENVIRONMENT_FRAMEWORK);
		$return .= "<script type='text/javascript'>\n$('#" . $picker_id . "').colorField(" . JSON::encode($params) . ");</script>";
		
    return $return;
  } // smarty_function_color_field