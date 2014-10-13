<?php

  /**
   * select_company helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select company box
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws InvalidInstanceError
   */
  function smarty_function_select_company($params, &$smarty) {
    $user = array_required_var($params, 'user', true, 'User');
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_company');
    } // if
    
    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Company --'), '');
      $options[] = option_tag('', '');
    } // if
    
    $exclude_ids = array_var($params, 'exclude', null, true);

    $exclude_owner_company = array_var($params, 'exclude_owner_company', null, true);
    if ($exclude_owner_company) {
      if (!is_array($exclude_ids)) {
        $exclude_ids = array();
      } // if
      $exclude_ids[] = Companies::findOwnerCompany()->getId();
    } // if
    
    if($exclude_ids) {
      if (!is_foreachable($exclude_ids)) {
        $exclude_ids = array($exclude_ids);
      } // if

      $visible_company_ids = array_diff($user->visibleCompanyIds(), $exclude_ids);
    } else {
      $visible_company_ids = $user->visibleCompanyIds();
    } // if

    $companies = is_foreachable($visible_company_ids) ? DB::execute('SELECT id, name, state FROM ' . TABLE_PREFIX . 'companies WHERE id IN (?) AND state > ? ORDER BY name', $visible_company_ids, STATE_ARCHIVED) : null;
    
    if(is_foreachable($companies)) {
      $archived_companies = array();
      
      $user_company_id = $user->getCompanyId();
      
      foreach($companies as $company) {
        if($company['state'] == STATE_ARCHIVED && $company['id'] != $user_company_id) {
          $archived_companies[] = option_tag($company['name'], $company['id'], array(
            'selected' => $value == $company['id'], 
          ));
        } else {
          $options[] = option_tag($company['name'], $company['id'], array(
            'class' => 'object_option', 
            'selected' => $value == $company['id'], 
          ));
        } // if
      } // foreach
      
      if(count($archived_companies)) {
        if(count($archived_companies) != count($companies)) {
          $options[] = option_tag('', '');
        } // if
        
        $options[] = option_group_tag(lang('Archive'), $archived_companies);
      } // if
    } // if
    
    if(array_var($params, 'can_create_new', true) && Companies::canAdd($user)) {
      $js_options = JSON::encode(array(
        'add_object_url' => Router::assemble('people_companies_add'),
        'object_name' => 'company', 
        'add_object_message' => lang('Please insert a new company name'), 
        'on_new_object' => isset($params['on_new_company']) ? $params['on_new_company'] : null,
      	'success_event' => isset($params['success_event']) ? $params['success_event'] : null
      ));
    } else {
      $js_options = '{}';
    } // if

    AngieApplication::useWidget('select_named_object', ENVIRONMENT_FRAMEWORK);
    return HTML::select($name, $options, $params) . '<script type="text/javascript">$("#' . $params['id'] . '").selectNamedObject("init", ' . $js_options . ')</script>';
  } // smarty_function_select_company