<?php

  /**
   * assignment_filters helper implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render assignment filters
   * 
   * @param array $params
   * @param Smarty $smarty
   */
  function smarty_function_assignment_filters($params, &$smarty) {
    $user = array_required_var($params, 'user', null, 'User');
    $filter = array_var($params, 'filter', null, true);
    
    $view = $smarty->createTemplate(AngieApplication::getViewPath('_assignment_filters', 'assignment_filters', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
    
    $projects = Projects::getIdNameMap($user, STATE_ARCHIVED, null, null, true);
    $labels = Labels::getIdDetailsMap('AssignmentLabel');

    if($user->isProjectManager() || $user->isPeopleManager()) {
      $companies = Companies::getIdNameMap(null, STATE_VISIBLE);
    } else {
      $visible_company_ids = $user->visibleCompanyIds();

      if($visible_company_ids) {
        $companies = Companies::getIdNameMap($visible_company_ids, STATE_VISIBLE);
      } else {
        $companies = array();
      } // if
    } // if
      
    $view->assign(array(
      'assignment_filters' => DataFilters::findByUser('AssignmentFilter', $user),
      'pre_select_filter' => $filter,
      'new_filter_url' => DataFilters::canAdd('AssignmentFilter', $user) ? Router::assemble('assignment_filters_add') : null,
      'users' => Users::getForSelect($user),
      'companies' => $companies,
      'projects' => $projects,
      'active_projects' => Projects::getIdNameMap($user, STATE_VISIBLE, null, null, true), // We need this, so we can group projects in projects picker
      'project_slugs' => $projects ? Projects::getIdSlugMap(array_keys($projects)) : null,
      'project_categories' => Categories::getIdNameMap(null, 'ProjectCategory'), 
      'labels' => empty($labels) ? null : $labels,
      'categories' => $projects ? Categories::getUniqueNamesInProjects(array_keys($projects), array('TaskCategory', 'TodoListCategory')) : array(),
      'milestones' => count($projects) ? Milestones::getUniqueNames(array_keys($projects), STATE_VISIBLE) : array(),
      'job_types' => AngieApplication::isModuleLoaded('tracking') ? JobTypes::getIdNameMap(null, JOB_TYPE_INACTIVE) : null,
    ));
      
    return $view->fetch();
  } // smarty_function_assignment_filters