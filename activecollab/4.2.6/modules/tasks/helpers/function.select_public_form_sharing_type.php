<?php

  /**
   * select_public_form_sharing_type helper implementation
   * 
   * @package activeCollab.modules.tasks
   * @subpackage helpers
   */

  /**
   * Render select public form sharing type
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_public_form_sharing_type($params, &$smarty) {
  	$widget_id = HTML::uniqueId('public_form_sharing_widget');
  	
  	$name = array_var($params, 'name');
  	$value = array_var($params, 'value');
  	
  	$expiration_name = array_var($params, 'expiration_name');
		$expiration_value = array_var($params, 'expiration_value');
				  	
		$expiration_options = array();
  	$expiration_days = array(1, 3, 7, 10, 14, 30, 45, 60);
  	
  	foreach ($expiration_days as $expiration_day) {
  		if ($expiration_day == $expiration_value) {
				$expiration_options[] = option_tag($expiration_day, $expiration_day, array(
					'selected' => 'selected'
				));
  		} else {
  			$expiration_options[] = option_tag($expiration_day);
  		} // if
  	} // foreach

  	$options = array(
  		'0' => lang("Don't share"),
  		'1'	=> lang('Share Indefinitely'),
  		'2' => lang('Share and Expire after')
  	);
  	
  	$result = '<div class="public_form_sharing_type" id="' . $widget_id . '">';
  	foreach ($options as $option_value => $option_text) {
  		$option_id = HTML::uniqueId('public_form_sharing_type_radio');
  		$result.= '<div>' . radio_field($name, $value == $option_value, array(
  			'id'		=> $option_id,
        'value' => $option_value,
        'class' => '', 
      )) . ' ' . label_tag($option_text, $option_id, false, array(), '');
      
      if ($option_value == 2) {
      	$result.= '<br />' . select_box($expiration_options, array(
      		'name' => $expiration_name,
      		'class' => 'select_relative_expiration_date'
      	)) . ' ' . lang('days');
      } // if
      
      $result.= '</div>';
  	} // foreach
  	$result.= '</div>';

    AngieApplication::useWidget('select_public_form_sharing_type', TASKS_MODULE);
  	$result.= '<script type="text/javascript">App.widgets.SelectPublicFormSharingType.init("' . $widget_id . '")</script>';
  	
  	return $result;
  } // select_public_form_sharing_type