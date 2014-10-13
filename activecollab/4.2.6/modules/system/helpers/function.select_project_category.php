<?php

  /**
   * Select project category implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render select project category widget
   *
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_select_project_category($params, &$smarty) {
    AngieApplication::useHelper('select_category', CATEGORIES_FRAMEWORK);
    
    $user = array_var($params, 'user');
    if(!($user instanceof User)) {
      throw new InvalidInstanceError('user', $user, '$user is expected to be User instance');
    } // if
    
    if(array_var($params, 'can_create_new', true) && $user->isProjectManager()) {
      $params['add_url'] = Router::assemble('project_categories_add');
    } // if
    
    $params['type'] = 'ProjectCategory';
    
    return smarty_function_select_category($params, $smarty);
  } // smarty_function_select_project_category
