<?php

  /**
   * milestone_filters helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render milestone filters
   * 
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_milestone_filters($params, &$smarty) {
    $user = array_required_var($params, 'user', null, 'User');
    $filter = array_var($params, 'filter', null, true);
    
    $view = $smarty->createTemplate(AngieApplication::getViewPath('_milestone_filters', 'milestone_filters', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
    
    $projects = Projects::getIdNameMap($user, STATE_ARCHIVED, null, null, true);
      
    $view->assign(array(
      'milestone_filters' => DataFilters::findByUser('MilestoneFilter', $user),
      'pre_select_filter' => $filter,
      'new_filter_url' => DataFilters::canAdd('MilestoneFilter', $user) ? Router::assemble('milestone_filters_add') : null,
      'users' => Users::getForSelect($user),
      'companies' => Companies::getIdNameMap(null, STATE_VISIBLE),
      'projects' => $projects,
      'active_projects' => Projects::getIdNameMap($user, STATE_VISIBLE, null, null, true), // We need this, so we can group projects in projects picker
      'project_slugs' => $projects ? Projects::getIdSlugMap(array_keys($projects)) : null,
      'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'),
    ));
      
    return $view->fetch();
  } // smarty_function_milestone_filters