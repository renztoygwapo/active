<?php

  // We need projects controller
  AngieApplication::useController('tasks', TASKS_MODULE);

  /**
   * Tasks controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class RelatedTasksController extends TasksController {
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if(!$this->active_task->isAccessible()) {
        $this->response->notFound();
      } // if

      if(!$this->request->isAsyncCall()) {
        $this->response->badRequest();
      } // if
    } // __construct
    
    /**
     * Show tasks index page
     */
    function index() {
      $this->response->assign(array(
        //'related_tasks' => $this->active_task->relatedTasks()->get($this->logged_user),
        'can_manage_related_tasks' => $this->active_task->relatedTasks()->canManage($this->logged_user),
        'remove_related_task_url' => $this->active_task->relatedTasks()->getRemoveTaskUrl('--TASK-ID--'),
        'task_url' => Router::assemble('project_task', array(
          'project_slug' => '--PROJECT-ID--',
          'task_id' => '--TASK-ID--',
        ))
      ));
    } // index

    /**
     * Add related task
     */
    function add_task() {
      if($this->request->isSubmitted()) {
        if($this->active_task->canEdit($this->logged_user)) {
          $related_task_project_id = (integer) $this->request->post('related_task_project_id');
          $related_task_id = (integer) $this->request->post('related_task_id');

          if($related_task_project_id && $related_task_id) {
            $related_task_project = Projects::findById($related_task_project_id);

            if($related_task_project instanceof Project && $related_task_project->isAccessible() && $related_task_project->canView($this->logged_user)) {
              $related_task = Tasks::findByTaskId($related_task_project, $related_task_id);

              if($related_task instanceof Task && $related_task->isAccessible() && $related_task->canView($this->logged_user)) {
                try {
                  $this->active_task->relatedTasks()->addTask($related_task, $this->request->post('relation_note'), $this->logged_user);
                  AngieApplication::cache()->removeByObject($this->active_task);
                  $this->response->respondWithData($this->active_task, array(
                    'as' => 'task',
                    'detailed' => true,
                  ));
                } catch(Exception $e) {
                  $this->response->exception($e);
                } // try
              } else {
                $this->response->notFound();
              } // if
            } else {
              $this->response->notFound();
            } // if
          } else {
            $this->response->badRequest();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // add_task

    /**
     * Remove specific task
     */
    function remove_task() {
      if($this->request->isSubmitted()) {
        if($this->active_task->canEdit($this->logged_user)) {
          $related_task_id = $this->request->getId('related_task_id');
          $related_task = $related_task_id ? Tasks::findById($related_task_id) : null;

          if($related_task instanceof Task) {
            try {
              $this->active_task->relatedTasks()->removeTask($related_task);
              AngieApplication::cache()->removeByObject($this->active_task);
              $this->response->respondWithData($this->active_task, array(
                'as' => 'task',
                'detailed' => true,
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // remove_task
  
  }