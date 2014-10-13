<?php

  /**
   * editor_field helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render HTML editor
   *
   * @param array $params
   * @param string $content
   * @param Smarty $smarty
   * @param boolean $repeat
   * @return string
   */
  function smarty_block_editor_field($params, $content, &$smarty, &$repeat) {
    if($repeat) {
      return;
    } // if
    
    // Determine if we need to use visual editor or textarea
    if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_DEFAULT) {
      $visual = isset($params['visual']) ? (boolean) isset($params['visual']) : true;
    } else {
      $visual = false;
    } // if
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('visual_editor');
    } // if
    
    $name_parameter = array_var($params, 'name');
    $variable_name = substr($name_parameter, 0, strrpos($name_parameter, '['));
    $return_string = '';
    
    if($visual) {
      AngieApplication::useWidget('editor', VISUAL_EDITOR_FRAMEWORK);

      $label = array_var($params, 'label', null, true);
      
      if($label) {
        $return_string .= HTML::label($label, null, isset($params['required']) && $params['required'], array('class' => 'main_label'));
      } // if
      
      $buttons = array_var($params, 'buttons', null);

      $editor_params = array(
      	'name'         			        => $name_parameter,
      	'value'        			        => $content,
      	'vertical_resize'		        => array_var($params, 'resize', false),
      	'embeded_name'			        => $variable_name ? $variable_name . '[embeded_objects]' : 'embeded_objects',
				'required'					        => isset($params['required']) && $params['required'],
        'headings_enabled'          => array_var($params, 'headings_enabled', true),
        'images_enabled'            => array_var($params, 'images_enabled', true),
        'code_enabled'              => array_var($params, 'code_enabled', true),
        'macros_enabled'            => array_var($params, 'macros_enabled', true),
        'tables_enabled'            => array_var($params, 'tables_enabled', true),
        'text_alignment_enabled'    => array_var($params, 'text_alignment_enabled', true),
        'lists_enabled'             => array_var($params, 'lists_enabled', true),
        'formatting_enabled'        => array_var($params, 'formatting_enabled', true),
        'text_style_enabled'        => array_var($params, 'text_style_enabled', true),
	      'ajax_submit_enabled'       => array_var($params, 'ajax_submit_enabled', true)
      );

	    // show link to item based on is user logged
	    $user = Authentication::getLoggedUser();
	    $editor_params['link_to_item_enabled'] = $user instanceof User;

      if ($buttons) {
        $editor_params['buttons'] = $buttons;
      } // if

	    // get object
      $object = array_var($params, 'object', null, true);

	    if ($object instanceof ProjectObject) {
		    $editor_params['quick_search_filters'] = array(
			    array('label' => lang('Only items in this project'), 'params' => array('project_id' => $object->getProjectId()))
		    );
	    } elseif ($object instanceof Project) {
		    $editor_params['quick_search_filters'] = array(
			    array('label' => lang('Only items in this project'), 'params' => array('project_id' => $object->getId()))
		    );
	    } // if

	    if ($object instanceof ISubscriptions && AngieApplication::getName() == 'activeCollab') {
	      $editor_params['mentions_enabled'] = true;

		    if ($object instanceof ProjectObject) {
			    $available_users = $object->getProject()->users()->get();
		    } else {
			    $available_users = $object->subscriptions()->getAvailableUsers($user);
		    } // if

				$owner_company = Companies::findOwnerCompany();

		    $company_ids = objects_array_extract($available_users, 'getCompanyId');
		    $companies_map = Companies::getIdNameMap($company_ids);

		    $owner_company_members = array();
		    $other_companies = array();
		    $individuals = array();

		    if (is_foreachable($available_users)) {
			    foreach ($available_users as $available_user) {
				    if (!($available_user instanceof User)) {
					    continue;
				    } // if

				    $company_id = $available_user->getCompanyId();
				    $company_name = $companies_map && isset($companies_map[$company_id]) ? $companies_map[$company_id] : null;

				    $prepared_user =  array(
					    'name' => $available_user->getName(),
					    'id' => $available_user->getId(),
					    'company_id' => $company_id,
					    'avatar_url' => $available_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL)
				    );

				    if ($company_name) {
					    if ($company_id == $owner_company->getId()) {
						    $owner_company_members[] = $prepared_user;
					    } else {
						    if (isset($other_companies[$company_name])) {
							    $other_companies[$company_name][] = $prepared_user;
						    } else {
							    $other_companies[$company_name] = array($prepared_user);
						    } // if
					    } // if
				    } else {
					    $individuals[] = $prepared_user;
				    } // if
			    } // foreach

			    ksort($other_companies);

			    // Join elements together (owner company first, than other companies and finally individuals)
			    $user_groups = array();

			    if(count($owner_company_members)) {
				    $user_groups[$owner_company->getName()] = $owner_company_members;
			    } // if

			    if(count($other_companies)) {
				    $user_groups = array_merge($user_groups, $other_companies);
			    } // if

			    if(count($individuals)) {
				    $user_groups[lang('Individuals')] = $individuals;
			    } // if
		    } // if

		    $editor_params['user_groups'] = $user_groups;
	    } // if

      $return_string .= '<div id="' . $params['id'] . '"></div><script type="text/javascript">$("#' . $params['id'] . '").visualEditor(' . JSON::encode($editor_params) . ')</script>';
    } else {
      if(isset($params['class'])) {
        $params['class'] .= ' editor';
      } else {
        $params['class'] = 'editor';
      } // if

      $return_string .= HTML::textarea(@$params['name'], $content, $params);
    } // if
    
    return $return_string;
  } // smarty_block_editor_field