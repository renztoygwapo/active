<?php

  /**
   * select_category helper
   *
   * @package activeCollab.modules.resources
   * @subpackage helpers
   */
  
  /**
   * Render select category control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_category($params, &$smarty) {
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    $parent = array_var($params, 'parent', null, true);
    $category_type = array_var($params, 'type', null, true);

  	$label_type = strtolower(array_var($params, 'label_type', null, true));
  	if ($label_type == 'inner') {
  		$control_label = array_var($params, 'label', null, true);
  	} else {
  		$control_label = null;
  	} // if
    
    if(isset($params['user'])) {
      unset($params['user']);
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_category');
    } // if
    
    if(isset($params['class']) && $params['class']) {
      $params['class'] .= ' select_category';
    } else {
      $params['class'] = 'select_category';
    } // if
    
    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    
    // Prepare options
    $options = array();

    $categories = Categories::getIdNameMap($parent, $category_type);
      
    if(is_foreachable($categories)) {
      foreach($categories as $id => $cat_name) {
        $options[] = HTML::optionForSelect($cat_name, $id, $id == $value, array(
          'class' => 'object_option',  
        ));
      } // foreach
    } // if
    
    // Default interface
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      $add_url = array_var($params, 'add_url', false, true);
      
      if($add_url) {
        $js_options = JSON::encode(array(
          'add_object_url' => $add_url, 
          'object_name' => 'category', 
          'add_object_message' => lang('Please insert new category name'),
          'on_new_object' => isset($params['on_new_category']) ? $params['on_new_category'] : null,
          'success_event' => isset($params['success_event']) ? $params['success_event'] : null,
          'additional_event_params' => array(
            'context'  => $parent instanceof ApplicationObject ? ($parent->fieldExists('id') ? get_class($parent) . '_' . $parent->getId() : get_class($parent)) : null,
            'type'     => $category_type
          )
        ));
      } else {
        $js_options = '{}';
      } // if
    } // if
    
		if ($control_label) {
			if (array_var($params, 'optional', true, true)) {
				$options = array_merge(array(
					HTML::optionForSelect(lang('No Category')),
					HTML::optionForSelect(''),
				), $options);
			} // if			
			return HTML::select($name, HTML::optionGroup($control_label, $options, array('class' => 'centered')), $params);
		} else {
	    $result = array_var($params, 'optional', true, true) ? 
	      HTML::optionalSelect($name, $options, $params, lang('None')) : 
	      HTML::select($name, $options, $params);
		} // if
        
    if($interface == AngieApplication::INTERFACE_DEFAULT) {
      AngieApplication::useWidget('select_named_object', ENVIRONMENT_FRAMEWORK);
      $result .= '<script type="text/javascript">$("#' . $params['id'] . '").selectNamedObject("init", ' . $js_options . ')</script>';
    } // if
    
    return $result;
  } // smarty_function_select_category