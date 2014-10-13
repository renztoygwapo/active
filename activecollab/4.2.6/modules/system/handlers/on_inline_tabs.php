<?php

  /**
   * System module on_inline_tabs event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Handle on inline tabs event
   *
   * @param NamedList $tabs
   * @param ApplicationObject $object
   * @param User $logged_user
   * @param string $interface
   * @return null
   */
  function system_handle_on_inline_tabs(&$tabs, &$object, &$logged_user, $interface) {
    // populate user inline tabs
    if ($object instanceof User) {
        $tabs->add('user_projects', array(
          'title' => lang('Projects'),
          'url'  => $object->getProjectsUrl()
        ));        
      
      if ($object->canViewActivities($logged_user)) {
        $tabs->add('user_recent_activities', array(
          'title' => lang('Recent Activities'),
          'url' => $object->getRecentActivitiesUrl()
        ));
      } // if
    } // if User
    
    // populate company inline tabs
    if ($object instanceof Company) {
      $tabs->add('projects', array(
        'title' => lang('Projects'),
        'url' => $object->getProjectsUrl(),
      ));
      
      if($logged_user->isPeopleManager() || $logged_user->isFinancialManager()) {
        $tabs->add('archived_users', array(
          'title' => lang('Archived Users'),
          'url' => $object->getArchivedUsersUrl(),
          'count' => $object->getArchivedUsersCount()
        ));
      } // if

      if ($logged_user->isAdministrator() || ProjectRequests::canRequestProjectsFor($logged_user->getCompany(), $logged_user)) {
        $tabs->add('project_requests', array(
          'title' => lang('Project Requests'),
          'url'   => $object->getProjectRequestsUrl(),
          'count' => ProjectRequests::countByCompany($object)
        ));
      } // if
    } // if company
    
    // populate milestone inline tabs
    if($object instanceof Milestone) {
      $milestone_sections = new NamedList();
      $project = $object->getProject();
      EventsManager::trigger('on_milestone_sections', array(&$project, &$object, &$logged_user, &$milestone_sections, $interface));

      // nasty way to sort the list, but atm no other way to do it
      // move tabs in this order: tasks/todo lists/everything else
      if (isset($milestone_sections['todo_lists'])) {
        $todo_lists = $milestone_sections['todo_lists'];
        $milestone_sections->remove('todo_lists');
        $milestone_sections->beginWith('todo_lists', $todo_lists);
      } // if

      if (isset($milestone_sections['tasks'])) {
        $tasks = $milestone_sections['tasks'];
        $milestone_sections->remove('tasks');
        $milestone_sections->beginWith('tasks', $tasks);
      } // if

      if(is_foreachable($milestone_sections)) {
        foreach($milestone_sections as $section_id => $section) {
          $tabs->add($section_id, array(
            'title' => $section['text'],
            'url' => $section['url'],
            'options' => $section['options']
          ));
        } // foreach
      } // if      
   } // if Milestone
  } // system_handle_on_inline_tabs