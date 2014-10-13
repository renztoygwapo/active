<?php

  /**
   * Render checkbox input
   * 
   * @param string $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_checkbox($params, &$smarty) {
    $name = array_required_var($params, 'name');
    $checked = array_var($params, 'checked', false, true);
    $label = array_var($params, 'label', null, true);
    
    if(!array_key_exists('value', $params)) {
      $params['value'] = 'checked';
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('checkbox');
    } // if
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    // Default web browser request
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
    	if($label) {
	      return '<div class="checkbox_wrapper">' . HTML::checkbox($name, $checked, $params) . ' ' . HTML::label($label, $params['id'], false, null, '') . '</div>';
	    } else {
	      return '<div class="checkbox_wrapper">' . HTML::checkbox($name, $checked, $params) . '</div>';
	    } // if
	    
	  // Request made by phone device
    } elseif($interface == AngieApplication::INTERFACE_PHONE) {
    	$is_checked = !$checked ? 'off' : 'on';
    	$init_value = $is_checked == on ? 'checked' : '';
	  	
	  	$result = '<div id="' . $params['id'] . '" class="form_checkbox ' . $is_checked . '">
				<span>' . $label . '</span>
				<input type="hidden" name="' . $name . '" value="' . $init_value . '" class="selected_value" />
			</div>';
	  	
	  	return $result . '<script type="text/javascript">App.widgets.checkboxControl.init("' . $params['id'] . '");</script>';
    } // if
  } // smarty_function_checkbox