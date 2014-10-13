<?php

  /**
   * project_progress helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render project progress bar
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_progress($params, &$smarty) {
    $project = array_required_var($params, 'project', true, 'Project');
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    $smarty->assign(array(
      '_project_progress' => $project,
      '_project_progress_info' => (boolean) array_var($params, 'info', true),
      '_project_progress_label_on_right' => array_var($params, 'label')
    ));
    
    return $smarty->fetch(get_view_path('_projects_progress', 'project', SYSTEM_MODULE, $interface));
  } // smarty_function_project_progress