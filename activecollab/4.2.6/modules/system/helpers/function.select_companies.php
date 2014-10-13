<?php

  /**
   * Select companies helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Select companies helper
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_companies($params, &$smarty) {
    $user = array_required_var($params, 'user', true);

    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    
    if(empty($params['id'])) {
      $params['id'] = HTML::uniqueId('select_companies');
    } // if

    $exclude_ids = array_var($params, 'exclude', null, true);

    $exclude_owner_company = array_var($params, 'exclude_owner_company', null, true);
    if($exclude_owner_company) {
      if(!is_array($exclude_ids)) {
        $exclude_ids = array();
      } // if
      $exclude_ids[] = Companies::findOwnerCompany()->getId();
    } // if

    if($exclude_ids) {
      if(!is_foreachable($exclude_ids)) {
        $exclude_ids = array($exclude_ids);
      } // if

      $visible_company_ids = array_diff($user->visibleCompanyIds(), $exclude_ids);
    } else {
      $visible_company_ids = $user->visibleCompanyIds();
    } // if

    $companies = array();
    if (is_foreachable($visible_company_ids)) {
      $companies = DB::execute('SELECT id, name FROM ' . TABLE_PREFIX . 'companies WHERE id IN (?) AND state >= ? ORDER BY name', $visible_company_ids, STATE_VISIBLE);
    } // if

    if(is_foreachable($companies)) {
      $possibilities = array();

      foreach($companies as $company) {
        $possibilities[$company['id']] = $company['name'];
      } //foreach

      return HTML::checkboxGroupFromPossibilities($name, $possibilities, $value, $params);
    } else {
      return '<p>' . lang('There are no companies defined') . '</p>';
    } //if
  } // smarty_function_select_companies