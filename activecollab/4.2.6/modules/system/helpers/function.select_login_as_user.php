<?php

  /**
   * select_login_as_user helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render select login as user control
   * 
   * Parameters:
   * 
   * - user -  Instance of user accesing the page (required)
   * - exclude_user_ids - Array of user ids that need to be excluded
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_select_login_as_user($params, &$smarty) {
    static $ids = array();
    
    $user = array_var($params, 'user');
    if(!($user instanceof IUser)) {
      throw new InvalidInstanceError('user', $user, 'IUser');
    } // if
    
    $exclude_user_ids = array_var($params, 'exclude_user_ids', null);

		$visible_company_ids = $user->visibleCompanyIds();
    $visible_user_ids = $user->visibleUserIds();
    
    $companies = DB::execute('SELECT id, name, state FROM ' . TABLE_PREFIX . 'companies WHERE id IN (?) AND state >= ? ORDER BY is_owner DESC, name ASC', $visible_company_ids, STATE_ARCHIVED);
    $grouped_users = array();
    
    if(is_foreachable($companies)) {
    	$company_ids = array();
    	foreach($companies as $company) {
    		$company_ids[] = $company['id'];
    	} // if
    	
    	if($exclude_user_ids) {
    		$visible_user_ids = array_diff($visible_user_ids, $exclude_user_ids);
    	} // if
    	
    	$visible_users = DB::execute('SELECT id, first_name, last_name, email, company_id FROM ' . TABLE_PREFIX . 'users WHERE id IN (?) AND company_id IN (?)', $visible_user_ids, $company_ids);
    	
    	foreach($visible_users as $visible_user) {
    		$company = Companies::findById($visible_user['company_id']);
    		$visible_user_instance = Users::findById($visible_user['id']);
    		
    		$grouped_users[$company->getName()][$visible_user_instance->getId()] = $visible_user_instance->getDisplayName();
    	} // foreach
    } // if
    
    $id = array_var($params, 'id');
    if(empty($id)) {
      $counter = 1;
      
      do {
        $id = 'select_user_' . $counter++;
      } while(in_array($id, $ids));
    } // if
    
    $ids[] = $id;
    
    $value = array_var($params, 'value');
    
    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select User --'), '');
      $options[] = option_tag('', '');
    } // if
    
    if(is_foreachable($grouped_users)) {
      foreach($grouped_users as $company_name => $users) {
        $company_users = array();
        
        foreach($users as $user_id => $user_display_name) {
          $company_users[] = option_tag($user_display_name, $user_id, array(
            'selected' => $user_id == $value, 
          ));
        } // foreach
        
        $options[] = option_group_tag($company_name, $company_users);
      } // foreach
    } // if
    
    return select_box($options, $params);
  } // smarty_function_select_login_as_user