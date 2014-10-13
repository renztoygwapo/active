<?php

  /**
   * select_interface helper implementation
   * 
   * @package angie.frameworks.environment
   * @subpackage helpers
   */

  /**
   * Render select_interface box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_interface($params, Smarty $smarty) {
    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    $label = array_var($params, 'label', null, true);
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $possibilities = array(
  	  AngieApplication::INTERFACE_DEFAULT => lang('Default'), 
  	  AngieApplication::INTERFACE_PHONE => lang('Phone'), 
  	  AngieApplication::INTERFACE_TABLET => lang('Tablet'), 
  	);
    
    // Phone inerface
  	if($interface == AngieApplication::INTERFACE_PHONE) {
			$result = '<div data-role="fieldcontain" class="select_interface">
  			<fieldset data-role="controlgroup" data-type="horizontal">';
			
			foreach($possibilities as $k => $possibility) {
				if($k == AngieApplication::INTERFACE_TABLET) {
					continue;
				} // if
				
				$checked = false;
				if(isset($value) && $value == $k || AngieApplication::INTERFACE_PHONE == $k) {
					$checked = true;
				} // if
				
				$result .= label_tag('', 'interface_'.$k, false, array('class' => 'inline'), '').' '.
  				HTML::openTag('input', array(
  					'type' => 'radio',
  					'class' => 'inline',
  					'id' => 'interface_'.$k,
  				  'name' => $name,
  				  'value' => $k,
  					'checked' => $checked,
  					'data-theme' => 'i'
  				));
			} // foreach
  				
  		$result .= '
  			</fieldset>
  		</div>';
	          
	    return $result;
	    
	  // Other interfaces
    } else {
	  	$smarty->assign(array(
	  		'_interface_id' => HTML::uniqueId('select_interface'),
	  		'_interface_possibilities' => $possibilities,
	  		'_interface_current' => $interface,
	  		'_interface_name' => $name
	  	));
	  	
	  	return $smarty->fetch(get_view_path('_select_interface', null, ENVIRONMENT_FRAMEWORK));
    } // if
  } // smarty_function_select_interface