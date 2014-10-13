<?php

  /**
   * on_reports_panel event handler
   * 
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Handle on_reports_panel event
   * 
   * @param ReportsPanel $panel
   * @param User $user
   */
  function tasks_handle_on_reports_panel(ReportsPanel &$panel, User &$user) {
    if($user->isProjectManager()) {
      $panel->addTo('assignments', 'workload_report', lang('Workload'), Router::assemble('workload_reports'), AngieApplication::getImageUrl('reports/workload.png', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      $panel->addTo('assignments', 'aggregated_tasks', lang('Aggregate Tasks'), Router::assemble('project_tasks_aggregated_report'), AngieApplication::getImageUrl('common/chart-pie.png', REPORTS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));$panel->addTo('assignments', 'task_segments', lang('Task Segments'), Router::assemble('task_segments'), AngieApplication::getImageUrl('reports/task-segments.png', TASKS_MODULE , AngieApplication::INTERFACE_DEFAULT));
      $panel->addTo('assignments', 'open_vs_completed_tasks_report', lang('Open vs Completed Tasks'), Router::assemble('open_vs_completed_tasks_reports'), AngieApplication::getImageUrl('reports/open-vs-completed-tasks.png', TASKS_MODULE , AngieApplication::INTERFACE_DEFAULT));
      $panel->addTo('assignments', 'weekly_created_tasks_report', lang('Created Tasks (Weekly)'), Router::assemble('weekly_created_tasks_reports'), AngieApplication::getImageUrl('reports/created-tasks.png', TASKS_MODULE , AngieApplication::INTERFACE_DEFAULT));
      $panel->addTo('assignments', 'weekly_completed_tasks_report', lang('Completed Tasks (Weekly)'), Router::assemble('weekly_completed_tasks_reports'), AngieApplication::getImageUrl('reports/completed-tasks.png', TASKS_MODULE , AngieApplication::INTERFACE_DEFAULT));
    } // if
  } // tracking_handle_on_reports_panel