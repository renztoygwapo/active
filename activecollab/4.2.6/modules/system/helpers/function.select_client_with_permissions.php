<?php

  /**
   * select_user helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select user control
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_client_with_permissions($params, &$smarty) {
    $permissions = explode(',', array_required_var($params, 'permissions', true));
    $company_select_id = array_required_var($params, 'company_select_id', false);
    
    if(isset($params['class'])) {
      $params['class'] .= ' select_user';
    } else {
      $params['class'] = 'select_user';
    } // if

    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_user');
    } // if
    
    $name = array_var($params, 'name', null, true);  
    $value = array_var($params, 'value', null, true);
    $optional = array_var($params, 'optional', false, true);
    
    $options = array();
    
    if($optional) {
      $optional_text = array_var($params, 'optional_text', lang('-- None --'), true);
      
      $result = HTML::optionalSelect($name, $options, $params, $optional_text);
    } else {
      $result = HTML::select($name, $options, $params);
    } // if
    
    $data['client_company_managers_url'] = Router::assemble('people_company_users_with_permissions', array(
      'company_id' => '--COMPANY-ID--',
    ));
    $data['permissions'] = $permissions;

    $data['company_select_id'] = $company_select_id;
    $data['value'] = $value;
    $data['optional'] = $optional;
    $data['skip_owners_without_finances'] = array_var($params, 'skip_owners_without_finances', false, true);
    $data['require_all_permissions'] = (boolean) array_var($params, 'require_all_permissions', true);

    AngieApplication::useWidget('select_client_with_permissions', SYSTEM_MODULE);
    return $result . '<script type="text/javascript">$("#' . $params['id'] . '").selectClientWithPermissions(' . JSON::encode($data) . ');</script>';
  } // smarty_function_select_client_with_permissions