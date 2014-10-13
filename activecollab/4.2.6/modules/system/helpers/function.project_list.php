<?php

  /**
   * List projects helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render project list 
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_list($params, &$smarty) {
    $projects = array_required_var($params, 'projects');
    $user = array_var($params, 'user'); 
    
    $smarty->assign(array(
      '_projects' => $projects,
      '_user' => $user
    ));
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    return $smarty->fetch(get_view_path('_project_list', 'project', SYSTEM_MODULE, $interface));
    
    
  } // smarty_function_project_progress