<?php

  /**
   * List user helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render user list 
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_user_list($params, &$smarty) {
    $users = array_required_var($params, 'users');
    $label = array_var($params, 'label', lang('Active Users'), true);
    $user = array_var($params, 'user'); 
    $archived = array_var($params, 'archived', false); 
    
    $smarty->assign(array(
      '_users' => $users,
      '_user' => $user,
      '_label' => $label,
      '_archived' => $archived
    ));
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    return $smarty->fetch(get_view_path('_user_list', 'users', SYSTEM_MODULE, $interface));
    
    
  } // smarty_function_user_list