<?php

  /**
   * select_manage_by helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select members
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   * @throws InvalidInstanceError
   */
  

  function smarty_function_select_manage_by($params, &$smarty) {
    
    $name = array_var($params, 'name', null, true);
    $value = array_var($params, 'value', null, true);
    $user_id = array_var($params, 'user_id', null, true);

    $options = array();
    if(array_var($params, 'optional', false, true)) {
      $options[] = option_tag(lang('-- Select Managed By --'), '');
      $options[] = option_tag('', '');
    } // if

    $data_UserType = array('Member', 'Administrator', 'Manager');
    $members = Users::getUsersByType($data_UserType);

    if(!empty($members)) {
    	foreach ($members as $member) {
        if($user_id != $member['id']) {
          $first_name = (isset($member['first_name']))? $member['first_name'] : '' ;
          $last_name = (isset($member['last_name']))? $member['last_name'] : '' ;
          $options[] = option_tag($first_name . ' ' .$last_name, $member['id'], array(
              'selected' => $value == $member['id'], 
              ));
        }
    	}
    }

    return HTML::select($name, $options, $params);
  }
