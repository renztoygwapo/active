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
  function smarty_function_project_milestone($params, &$smarty) {
    $project = array_required_var($params, 'project',true,'Project');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    $milestones = array_var($params,'milestones',false);
    
    $day_types = get_day_project_object_types();
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
    
    if($milestones) {
      $headline = lang('Milestones');
    } else {
      $upcoming = ProjectObjects::findUpcoming($user, $project, $day_types);
      $late = ProjectObjects::findLateAndToday($user, $project, $day_types);
      $milestones = $late;
      $headline = lang('Late / Today Milestones');
    }//if
    
    $smarty->assign(array(
      '_upcoming_objects' => $milestones,
      '_headline' => $headline
    ));
    $print = $smarty->fetch(get_view_path('_projects_milestone', 'project', SYSTEM_MODULE, $interface));
    
    if(isset($upcoming) && $upcoming) {
      $smarty->assign(array(
        '_upcoming_objects' => $upcoming,
        '_headline' => lang('Upcoming Milestones')
      ));
      $print .= $smarty->fetch(get_view_path('_projects_milestone', 'project', SYSTEM_MODULE, $interface));
    }//if
    
    return $print;
  } // smarty_function_project_progress