<?php

  /**
   * System module on_project_brief_stats event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */

  /**
   * Handle project brief stats
   *
   * @param Project $project
   * @param array $project_stats
   * @param User $logged_user
   */
  function system_handle_on_project_brief_stats(&$project, &$project_stats, &$logged_user) {
    $project_people_count = $project->users()->count($logged_user);
    
    if ($project_people_count) {
      if ($project_people_count == 1) {
        $project_stats[] = lang('<strong>:people_count</strong> person on project', array('people_count' => $project_people_count));
      } else {
        $project_stats[] = lang('<strong>:people_count</strong> people on project', array('people_count' => $project_people_count));
      } // if
    } // if
    
    $late_milestones_count = Milestones::countLate($logged_user, array($project->getId()), array('Milestone'));
    if ($late_milestones_count) {
      if($late_milestones_count == 1) {
        $project_stats[] = lang('<strong>1</strong> late milestone in this project');
      } else {
        $project_stats[] = lang('<strong>:milestones_count</strong> late milestones in this project', array('milestones_count' => $late_milestones_count));
      } // if
    } // if
    
    $today_milestones_count = Milestones::countToday($logged_user, array($project->getId()), array('Milestone'));
    if ($today_milestones_count) {
      if($today_milestones_count == 1) {
        $project_stats[] = lang('<strong>1</strong> milestone scheduled for today');
      } else {
        $project_stats[] = lang('<strong>:milestones_count</strong> milestones scheduled for today', array('milestones_count' => $today_milestones_count));
      } // if
    } // if
    
    if(AngieApplication::isModuleLoaded(TASKS_MODULE)) {
      $open_tasks = $project->getOpenTasksCount();
      if($open_tasks && (Tasks::canAccess($logged_user, $project))) {
        if($open_tasks == 1) {
          $project_stats[] = lang('<strong>1</strong> task open');
        } else {
          $project_stats[] = lang('<strong>:open_tasks_count</strong> tasks open', array('open_tasks_count' => $open_tasks));
        } // if
      } // if
    }//if
  } // system_handle_on_project_brief_stats