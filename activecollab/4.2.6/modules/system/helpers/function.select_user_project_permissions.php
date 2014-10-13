<?php

  /**
   * Render select project user permissions widget
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select user permissions widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_user_project_permissions($params, &$smarty) {
    $prefix = '';
    
    if(isset($params['label']) && $params['label']) {
      $prefix = HTML::label($params['label'], null, array_var($params, 'required', false), array('class' => 'main_label'));
    } // if
    
    $template = $smarty->createTemplate(get_view_path('_user_project_permissions', null, SYSTEM_MODULE));
    
    $template->assign(array(
      'project_roles' => ProjectRoles::getIdNameMap(), 
      'name' => array_required_var($params, 'name'), 
      'id' => $params['id'] ? $params['id'] : HTML::uniqueId('select_user_project_permissions'), 
      'role_id_field' => array_var($params, 'role_id_field', 'role_id'), 
      'permissions_field' => array_var($params, 'permissions_field', 'permissions'), 
      'role_id' => array_var($params, 'role_id', 0), 
      'permissions' => array_var($params, 'permissions', array()), 
    ));
    
    return $prefix . $template->fetch();
  } // smarty_function_select_user_project_permissions