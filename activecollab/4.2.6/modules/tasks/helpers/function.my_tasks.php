<?php

  /**
   * my_tasks helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */

  /**
   * Render my tasks widget
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_my_tasks($params, &$smarty) {
    AngieApplication::useWidget('my_tasks', TASKS_MODULE);

    if(AngieApplication::isModuleLoaded('tracking')) {
      AngieApplication::useWidget('object_time', TRACKING_MODULE);
    } // if

    $user = array_required_var($params, 'user', true, 'User');
    $interface = array_var($params, 'interface', AngieApplication::INTERFACE_DEFAULT);

    try {
      $assignments = Tasks::getMyTasksFilter($user)->run($user);
    } catch(DataFilterConditionsError $e) {
      $assignments = null;
    } // try

    $late_assignments = null;

    if(array_var($params, 'include_late_assignments')) {
      try {
        $late_assignments = Tasks::getMyLateTasksFilter($user)->run($user);
      } catch(DataFilterConditionsError $e) {

      } // try

      if($late_assignments !== null && isset($late_assignments['all']) && $late_assignments['all']) {
        $late_assignments = $late_assignments['all']['assignments'];
      } // if
    } // if

    $template = $smarty->createTemplate(AngieApplication::getViewPath('_render_my_tasks', 'my_tasks', TASKS_MODULE, $interface));
    $template->assign(array(
      'id' => isset($params['id']) && $params['id'] ? $params['id'] : HTML::uniqueId('my_tasks'),
      'assignments' => $assignments,
      'late_assignments' => $late_assignments,
      'labels' => Labels::getIdDetailsMap('AssignmentLabel', true),
      'project_slugs' => Projects::getIdSlugMap(),
      'user' => $user,
      'user_id' => $user->getId(),
      'urls' => array(
        'refresh' => Router::assemble('my_tasks_refresh'),
        'task_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--')) : '',
        'task_complete_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_complete', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--')) : '',
        'task_reopen_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_reopen', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--')) : '',
        'task_tracking_url' => AngieApplication::isModuleLoaded('tracking') ? Router::assemble('project_task_tracking', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--')) : '',
        'subtask_complete_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_subtask_complete', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--', 'subtask_id' => '--SUBTASK-ID--')) : '',
        'subtask_reopen_url' => AngieApplication::isModuleLoaded('tasks') ? Router::assemble('project_task_subtask_reopen', array('project_slug' => '--PROJECT-SLUG--', 'task_id' => '--TASK-ID--', 'subtask_id' => '--SUBTASK-ID--')) : '',
        'todo_url' => AngieApplication::isModuleLoaded('todo') ? Router::assemble('project_todo_list', array('project_slug' => '--PROJECT-SLUG--', 'todo_list_id' => '--TODO-LIST-ID--')) : ''
      )
    ));

    return $template->fetch();
  } // smarty_function_my_tasks