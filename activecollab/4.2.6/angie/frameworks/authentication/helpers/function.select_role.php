<?php

  /**
   * Select role helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select role helper
   * 
   * Params:
   * 
   * - name - Select name attribute
   * - value - ID of selected role
   * - optional - Wether value is optional or not
   * - active_user - Set if we are changing role of existing user so we can 
   *   handle situations when administrator role is displayed or changed
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_role($params, &$smarty) {
  	$name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    $active_user = array_var($params, 'active_user', false, true);
    
    $logged_user = Authentication::getLoggedUser();
    if(!($logged_user instanceof User)) {
      return new InvalidParamError('logged_user', $logged_user, '$logged_user is expected to be an instance of user class');
    } // if
    
    // Prepare options
    $options = array();
    
    $roles = Roles::find();
    if($roles) {
      foreach($roles as $role) {
        $show_role = true;
        
        if($role->getPermissionValue('has_admin_access') && !$logged_user->isAdministrator() && !$active_user->isAdministrator()) {
          $show_role = false; // don't show administration role to non-admins and for non-admins
        } // if
        
        if($show_role) {
          $options[] = HTML::optionForSelect($role->getName(), $role->getId(), $role->getId() == $value, array(
          	'class' => 'object_option'
          ));
        } // if
      } // foreach
    } // if
    
    $result = array_var($params, 'optional', false, true) ? 
      HTML::optionalSelect($name, $options, $params, lang('None')) : 
      HTML::select($name, $options, $params);
    
    return $result;
  } // smarty_function_select_role