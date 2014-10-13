<?php

  /**
   * milestone_list_objects helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render milestone link
   * 
   * Parameters:
   * 
   * - object - Milestone instance that need to be linked
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_milestone_list_objects($params, &$smarty) {
    $object = array_required_var($params, 'object', true, 'Milestone');
    $user = array_required_var($params, 'user', 'IUser');
    
    $type = array_required_var($params, 'type');
    
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface());
    
    switch($type) {
      case 'tasks':
        $_objects = Tasks::findByMilestone($object, STATE_VISIBLE, $user->getMinVisibility());
        $_title = lang('Tasks');
        break;
      case 'discussions':
        $_objects = Discussions::findByMilestone($object, STATE_VISIBLE, $user->getMinVisibility());
        $_title = lang('Discussions');
        break;
     case 'todo':
        $_objects = TodoLists::findByMilestone($object, STATE_VISIBLE, $user->getMinVisibility());
        $_title = lang('Todo Lists');
        break;
    } // switch
    
    $smarty->assign(array(
      '_objects' => $_objects,
      '_title' => $_title,
      '_type' => $type
    ));
    
    return $smarty->fetch(get_view_path('_milestone_list_objects', 'milestones', SYSTEM_MODULE, $interface));
  } // smarty_function_milestone_list_objects