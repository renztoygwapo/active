<?php

  // Extend milestones controller
  AngieApplication::useController('milestones', SYSTEM_MODULE);

  /**
   * Milestone tasks controller
   *
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class MilestoneTasksController extends MilestonesController {
    
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();
      
      if($this->active_milestone->isNew()) {
        $this->response->notFound();
      } // if
      
      $add_task_url = false;
      if(Tasks::canAdd($this->logged_user, $this->active_project)) {
        $add_task_url = Router::assemble('project_tasks_add', array(
          'project_slug' => $this->active_project->getSlug(), 
          'milestone_id' => $this->active_milestone->getId()
        ));
        
        $this->wireframe->actions->add('new_task', lang('New Task'), $add_task_url, array('icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface())));
      } // if
      
      $this->smarty->assign('add_task_url', $add_task_url);
    } // __construct
    
    /**
     * Show milestone tasks
     */
    function index() {
      // Serve request made with web browser
      if($this->request->isWebBrowser()) {
        $milestone_tasks_per_page = 30;

        $this->response->assign('more_results_url', Router::assemble('milestone_tasks', array(
          'project_slug' => $this->active_project->getSlug(),
          'milestone_id' =>$this->active_milestone->getId())
        ));

        if($this->request->get('paged_list')) {
          $exclude = $this->request->get('paged_list_exclude') ? explode(',', $this->request->get('paged_list_exclude')) : null;
          $timestamp = $this->request->get('paged_list_timestamp') ? (integer) $this->request->get('paged_list_timestamp') : null;
          $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Task' AND state >= ? AND visibility >= ? AND id NOT IN (?) AND created_on < ? ORDER BY " . Tasks::ORDER_ANY . " LIMIT $milestone_tasks_per_page", $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility(), $exclude, date(DATETIME_MYSQL, $timestamp));
          $this->response->respondWithData(Tasks::getDescribedTaskArray($result, $this->active_project, $this->logged_user, $milestone_tasks_per_page));
        } else {
          $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "project_objects WHERE milestone_id = ? AND type = 'Task' AND state >= ? AND visibility >= ? ORDER BY " . Tasks::ORDER_ANY, $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility());
          $tasks = Tasks::getDescribedTaskArray($result, $this->active_project, $this->logged_user, $milestone_tasks_per_page);
          $this->response->assign(array(
            'tasks' => $tasks,
            'milestone_tasks_per_page'  => $milestone_tasks_per_page,
            'total_items' => ($result instanceof DBResult) ? $result->count() : 0,
            'milestone_id' => $this->active_milestone->getId()
          ));
        } //if

      // Server request made with mobile device
      } elseif($this->request->isMobileDevice()) {
        $this->response->assign(array(
          'tasks' => DB::execute("SELECT id, name, category_id, milestone_id, integer_field_1 as task_id FROM " . TABLE_PREFIX . "project_objects WHERE type = 'Task' AND milestone_id = ? AND state >= ? AND visibility >= ? AND completed_on IS NULL ORDER BY " . Tasks::ORDER_OPEN, $this->active_milestone->getId(), STATE_VISIBLE, $this->logged_user->getMinVisibility()),
          'task_url' => Router::assemble('project_task', array('project_slug' => $this->active_project->getSlug(), 'task_id' => '--TASKID--')),
        ));

      // API call
      } elseif($this->request->isApiCall()) {
        $this->response->respondWithData(Tasks::findActiveByMilestone($this->active_milestone, $this->logged_user), array(
          'as' => 'tasks'
        ));
      } // if
    } // index
    
  } // MilestoneTasksController