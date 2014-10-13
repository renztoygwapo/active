<?php

  /**
   * project_exporter_user_link helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a user link
   *
   * Parameters:
   * 
   * - id - id of the user
   * - name - name of the user
   * - email - email of the user
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_user_link($params, $template) {  	
  	$user_link = null;
  	$user_id = array_var($params, 'id', null);
  	$user_name = array_var($params, 'name', null);
  	$user_email = array_var($params, 'email', null);
  	$user = ProjectExporterStorage::getUser($user_id);
		
  	if ($user instanceof User) {
   		$user_link = '<a href="' . $template->tpl_vars['url_prefix']->value . 'people/user_' . $user_id . '.html">' . clean($user->getDisplayName()) . '</a>';
  	} else {
			$user_link = '<a href="mailto:' . clean($user_email) . '">' . clean($user_name) . '</a>';
    } // if
    return $user_link;
  } // smarty_function_project_exporter_user_link