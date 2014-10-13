<?php

  // Build on top of system module
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project time and expenses controller
   *
   * @package activeCollab.modules.tracking
   * @subpackage controllers
   */
  class ProjectTrackingController extends ProjectController {
    
    /**
     * Active module
     *
     * @var string
     */
    protected $active_module = TRACKING_MODULE;
    
    /**
     * Before action
     */
    function __before() {
      parent::__before();
      
      if(!TrackingObjects::canAccess($this->logged_user, $this->active_project)) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->tabs->setCurrentTab('time');
      $this->wireframe->breadcrumbs->add('project_tracking', lang('Time and Expenses'), Router::assemble('project_tracking', array('project_slug' => $this->active_project->getId())));
    } // __before
    
    /**
     * Show project time and expenses log
     */
    function log() {
      
      // API response
      if($this->request->isApiCall()) {
        $limit = $this->request->get('dont_limit_result') ? null : 300;

        $this->response->respondWithData(TrackingObjects::findRecent($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility(), $limit), array(
          'as' => 'tracking_objects', 
        ));

      // Web browser or a phone
      } else if($this->request->isWebBrowser() || $this->request->isPhone()) {
      	if(TrackingObjects::canAdd($this->logged_user, $this->active_project)) {
      	  $this->wireframe->actions->add('new_time', lang('Log Time'), $this->active_project->tracking()->getAddTimeUrl(), array(
            'onclick' => new FlyoutFormCallback('time_record_created', array('width' => 'narrow')),
            'icon' => AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK) : AngieApplication::getImageUrl('icons/navbar/add-time.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE),
          ));
          
          $this->wireframe->actions->add('new_expense', lang('Log Expense'), $this->active_project->tracking()->getAddExpenseUrl(), array(
            'onclick' => new FlyoutFormCallback('expense_created', array('width' => 'narrow')),
            'icon' => AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK) : AngieApplication::getImageUrl('icons/navbar/add-expense.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE),
          ));
      	} // if

        // Web browser
        if($this->request->isWebBrowser()) {
          $this->wireframe->print->enable();
          
          $this->wireframe->actions->add('toggle_log_timesheet', lang('Show Timesheet'), Router::assemble('project_tracking_timesheet', array('project_slug' => $this->active_project->getSlug())));

          $items = TrackingObjects::findForTimeExpensesLog($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility());

          $parent_tasks = null;

          if($items && AngieApplication::isModuleLoaded('tasks')) {
            $parent_task_ids = array();

            foreach($items as $item) {
              if($item['parent_type'] == 'Task' && !in_array($item['parent_id'], $parent_task_ids)) {
                $parent_task_ids[] = $item['parent_id'];
              } // if
            } // foreach

            if(count($parent_task_ids)) {
              $rows = DB::execute("SELECT DISTINCT id, name, integer_field_1 AS 'task_id', completed_on FROM " . TABLE_PREFIX . 'project_objects WHERE id IN (?) AND type = ? AND state >= ?', $parent_task_ids, 'Task', STATE_ARCHIVED);
              if($rows) {
                $task_url = Router::assemble('project_task', array('project_slug' => $this->active_project->getSlug(), 'task_id' => '--TASK_ID--'));

                foreach($rows as $row) {
                  $parent_tasks[(integer) $row['id']] = array(
                    'name' => $row['name'],
                    'url' => str_replace('--TASK_ID--', $row['task_id'], $task_url),
                    'task_id' => (integer) $row['task_id'],
                    'is_completed' => !is_null($row['completed_on'])
                  );
                } // foreach
              } // if
            } // if
          } // if

          $this->response->assign(array(
          	'items' => $items,
            'parent_tasks' => $parent_tasks,
            'can_manage_items' => TrackingObjects::canManage($this->logged_user, $this->active_project, false),
            'totals' => TrackingObjects::findTotalsByProject($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility())
          ));

        // Phone
        } else {
          $this->response->assign('formatted_items', TrackingObjects::findForPhoneList($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility()));
        } // if

      // Print interface
      } else if($this->request->isPrintCall()) {
        $this->response->assign(array(
          'items' => TrackingObjects::findForPrintList($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility()),
          'project_currency' => $this->active_project->getCurrency(),
        ));
      }//if
    } // log

    /**
     * Handle mass updates
     */
    function log_mass_update() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(TrackingObjects::canManage($this->logged_user, $this->active_project)) {
          $new_billable_status = (integer) $this->request->post('new_billable_status');

          if($new_billable_status < BILLABLE_STATUS_NOT_BILLABLE || $new_billable_status > BILLABLE_STATUS_PAID) {
            $this->response->badRequest();
          } // if

          $time_record_ids = $this->request->post('time_record_ids') ? explode(',', $this->request->post('time_record_ids')) : array();
          $expense_ids = $this->request->post('expense_ids') ? explode(',', $this->request->post('expense_ids')) : array();

          $updated_time_records = array();
          $updated_expenses = array();

          try {
            DB::beginWork('Updating selected records @ ' . __CLASS__);

            if(count($time_record_ids)) {
              $time_records = TimeRecords::findByIds($time_record_ids);

              if($time_records) {
                foreach($time_records as $time_record) {
                  $time_record->setBillableStatus($new_billable_status);
                  $time_record->save();

                  $updated_time_records[] = $time_record->getId();
                } // foreach
              } // if
            } // if

            if(count($expense_ids)) {
              $expenses = Expenses::findByIds($expense_ids);

              if($expenses) {
                foreach($expenses as $expense) {
                  $expense->setBillableStatus($new_billable_status);
                  $expense->save();

                  $updated_expenses[] = $expense->getId();
                } // foreach
              } // if
            } // if

            DB::commit('Selected records have been updated @ ' . __CLASS__);
          } catch(Exception $e) {
            DB::rollback('Failed to updated selected records @ ' . __CLASS__);
            throw $e;
          } // if

          $this->response->respondWithData(array(
            'updated_time_records' => $updated_time_records,
            'updated_expenses' => $updated_expenses,
          ));
        } else {
          $this->response->forbidden();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // log_mass_update

    /**
     * Get tracking object totals
     */
    function log_get_totals() {
      if($this->request->isAsyncCall()) {
        $this->response->respondWithData(array(
          'totals' => TrackingObjects::findTotalsByProject($this->logged_user, $this->active_project, STATE_ARCHIVED, $this->logged_user->getMinVisibility())
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // log_get_totals
    
    /**
     * Display project timesheet
     */
    function timesheet() {
      $this->smarty->assign('timesheet', new ProjectTimesheet($this->logged_user, $this->active_project));
    } // timesheet
    
    /**
     * Show details for a single day in the timesheet
     */
    function timesheet_day() {
      $day = $this->request->get('day');
      if(empty($day)) {
        $this->response->notFound();
      } // if
      
      $day = new DateValue($day);
      
      $user_id = $this->request->getId('user_id');
      if($user_id) {
        $user = Users::findById($user_id);
        if($user instanceof User) {
          if(!$user->canView($this->logged_user)) {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if

        $this->smarty->assign(array(
          'records' => TrackingObjects::findUserTimeRecordsByDate($this->active_project, $day, $user, $this->logged_user, STATE_ARCHIVED),
          'active_user' => $user,
          'active_day' => $day,
          'can_add' => $this->active_project->tracking()->canAddFor($this->logged_user, $user),
          'default_billable_status' => ConfigOptions::getValueFor('default_billable_status', $this->active_project),
        ));
      } else {
        $this->response->notFound();
      } // if
    } // timesheet_day
    
  }