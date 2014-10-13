<?php

  // Build on top of framework controller
  AngieApplication::useController('fw_backend', ENVIRONMENT_FRAMEWORK);
  
  /**
   * Default controller for things that are behind login screen
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class BackendController extends FwBackendController {
  
    /**
     * Show dashboard overview
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->response->notFound(); // Don't show anything
      } // if

      // Phone homescreen
      if($this->request->isPhone()) {
        $homescreen_items = new NamedList(array(
          'people' => array(
            'text' => lang('People'),
        		'url' => Router::assemble('people'),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/people.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ), 
          'projects' => array(
            'text' => lang('Projects'),
        		'url' => Router::assemble('projects'),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/projects.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ), 
          'assignments' => array(
            'text' => lang('Assignments'),
        		'url' => Router::assemble('my_tasks'),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/assignments.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ), 
          'favorites' => array(
            'text' => lang('Favorites'),
        		'url' => $this->logged_user->getFavoritesUrl(),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/favorites.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ),
          'activities' => array(
            'text' => lang('Activities'),
        		'url' => Router::assemble('backend_activity_log'),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/recently.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ),
          'profile' => array(
            'text' => lang('Profile'),
        		'url' => $this->logged_user->getViewUrl(),
        		'icon' => AngieApplication::getImageUrl('icons/homescreen/profile.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
          ),
        ));
        
        EventsManager::trigger('on_phone_homescreen', array(&$homescreen_items, &$this->logged_user));
        
        $this->response->assign('homescreen_items', $homescreen_items);
        
        if(AngieApplication::isModuleLoaded('tracking')) {
        	$projects = Projects::findForQuickTracking($this->logged_user);
	        
	        if(is_foreachable($projects)) {
	        	$this->wireframe->actions->add('log_time', lang('Log Time'), '#', array(
		          'icon' => AngieApplication::getImageUrl('icons/navbar/add-time.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
		        ));
		        
		        $this->wireframe->actions->add('log_expenses', lang('Log Expense'), '#', array(
		          'icon' => AngieApplication::getImageUrl('icons/navbar/add-expense.png', TRACKING_MODULE, AngieApplication::INTERFACE_PHONE)
		        ));
	        } // if
	        
	        $this->response->assign('quick_tracking_data', array(
	        	'projects' => $projects,
	    			'time_records_add_url' => Router::assemble('project_tracking_time_records_add', array('project_slug' => '--PROJECT-SLUG--')),
	    			'expenses_add_url' => Router::assemble('project_tracking_expenses_add', array('project_slug' => '--PROJECT-SLUG--'))
	        ));
        } // if
        
        $this->wireframe->actions->add('quick_add', lang('Quick Add'), Router::assemble('quick_add'), array(
          'icon' => AngieApplication::getImageUrl('icons/navbar/add.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
        ));
        
        if(AngieApplication::isModuleLoaded('status') && StatusUpdates::canUse($this->logged_user)) {
        	$this->wireframe->actions->add('update_status', lang('Update Status'), Router::assemble('status_updates_add'), array(
	          'icon' => AngieApplication::getImageUrl('icons/navbar/comments.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE)
	        ));
        } // if
        
        $this->wireframe->actions->add('logout', lang('Logout'), Router::assemble('logout'), array(
    			'icon' => AngieApplication::getImageUrl('layout/buttons/logout.png', SYSTEM_MODULE, AngieApplication::INTERFACE_PHONE),
          'primary' => true
        ));
        
        $this->response->assign('logout_url', Router::assemble('logout'));

      // ---------------------------------------------------
      //  Regular web interface
      // ---------------------------------------------------

      } else {

        // Rebuild indexes
        if($this->logged_user->isAdministrator() && ConfigOptions::getValue('require_index_rebuild', false)) {
          $this->wireframe->tabs->add('require_index_rebuild', lang('Rebuild Indexes'), Router::assemble('homepage'), null, true);

          $this->setView(array(
            'module' => ENVIRONMENT_FRAMEWORK, 
            'controller' => null, 
            'view' => '_require_index_rebuild', 
          ));

          $this->response->assign('rebuild_indexes_url', Router::assemble('indices_admin_rebuild', array('quick' => true)));

        // Expired password
        } elseif($this->logged_user->isPasswordExpired()) {
          $this->wireframe->tabs->add('require_password_change', lang('Reset Password'), Router::assemble('homepage'), null, true);

          $this->setView(array(
            'module' => AUTHENTICATION_FRAMEWORK,
            'controller' => null,
            'view' => '_require_password_change',
          ));
          
        // Route the user to the page that they selected to be their first page
        } else {
          $default_homescreen_id = ConfigOptions::getValueFor('default_homescreen_tab_id', $this->logged_user);

          if(is_numeric($default_homescreen_id) && HomescreenTabs::tabExists($default_homescreen_id, $this->logged_user)) {
            $this->active_custom_tab = HomescreenTabs::findById($default_homescreen_id);

            $this->__forward('custom_tab');
          } elseif($default_homescreen_id == 'my_tasks') {
            $this->__forward('my_tasks');
          } elseif($default_homescreen_id == 'whats_new') {
            $this->__forward('whats_new');
          } else {
            if($this->logged_user instanceof Client) {
              $this->__forward('whats_new');
            } else {
              $this->__forward('my_tasks');
            } // if
          } // if
        } // if
      } // if
    } // index

    // ---------------------------------------------------
    //  Home Screen tabs functionality
    // ---------------------------------------------------

    /**
     * Selected custom tab
     *
     * @var HomescreenTab
     */
    private $active_custom_tab;

    /**
     * Prepare backend wireframe
     *
     * @param User $user
     * @param string $current
     * @return HomescreenTab[]
     */
    protected function prepareTabs(User $user, $current = null) {
      $this->wireframe->tabs->clear();

      $this->wireframe->tabs->add('whats_new', lang("What's New"), Router::assemble('whats_new'), null, $current == 'whats_new');

      if(!($user instanceof Client)) {
        $this->wireframe->tabs->add('my_tasks', lang('My Tasks'), Router::assemble('my_tasks'), null, $current == 'my_tasks');
      } // if

      $homescreen_tabs = $this->logged_user->homescreen()->getTabs();

      foreach($homescreen_tabs as $homescreen_tab) {
        if($homescreen_tab->isLoaded()) {
          $this->wireframe->tabs->add('homescreen_tab_' . $homescreen_tab->getId(), $homescreen_tab->getName(), $homescreen_tab->getHomescreenTabUrl(), null, $current == 'homescreen_tab_' . $homescreen_tab->getId());
        } // if
      } // foreach

      if($this->logged_user->homescreen()->canHaveOwn()) {
        $this->wireframe->tabs->addIcon('configure_homescreen', lang('Configure Home Screen'), $this->logged_user->homescreen()->getManageUrl(), AngieApplication::getImageUrl('icons/12x12/configure.png', HOMESCREENS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
      } // if

      return $homescreen_tabs;
    } // prepareTabs

    /**
     * Show my tasks page
     */
    function my_tasks() {
      if($this->request->isApiCall()) {
        try {
          $assignments = Tasks::getMyTasksFilter($this->logged_user)->run($this->logged_user);
        } catch(DataFilterConditionsError $e) {
          $assignments = null;
        } // try

        $this->response->respondWithData($assignments, array(
          'as' => 'assignments',
        ));
      } elseif($this->request->isWebBrowser()) {
        $this->prepareTabs($this->logged_user, 'my_tasks');

        $this->wireframe->actions->add('refresh', lang('Refresh'), Router::assemble('my_tasks_refresh'), array(
          'onclick' => new RefreshMyTasksCallback('my_tasks')
        ));

        $this->wireframe->actions->add('settings', lang('Settings'), Router::assemble('my_tasks_settings'), array(
          'onclick' => new FlyoutFormCallback(array(
            'success_event' => 'my_tasks_settings_updated',
            'width' => 450,
          )),
        ));

        $this->wireframe->print->enable();
      } elseif($this->request->isMobileDevice()) {
        $this->wireframe->breadcrumbs->add('my_tasks', lang('Assignments'), Router::assemble('my_tasks'));
      } elseif($this->request->isPrintCall()) {
        $filter = Tasks::getMyTasksFilter($this->logged_user);
        $filter->setAdditionalColumn1(AssignmentFilter::ADDITIONAL_COLUMN_DUE_ON);

        try {
          $assignments = $filter->run($this->logged_user);
        } catch(Exception $e) {
          $assignments = null;
        } // try

        $this->response->assign(array(
          'filter' => $filter,
          'assignments' => $assignments,
        ));
      } // if
    } // my_tasks

    /**
     * Show what's new page
     */
    function whats_new() {
      $this->prepareTabs($this->logged_user, 'whats_new');
      $this->response->assign('activity_logs', ActivityLogs::findRecent($this->logged_user));
    } // whats_new

    /**
     * Show a custom tab
     */
    function custom_tab() {
      if(empty($this->active_custom_tab)) {
        $homescreen_tab_id = $this->request->get('homescreen_tab_id');

        if($homescreen_tab_id) {
          $this->active_custom_tab = HomescreenTabs::findById($homescreen_tab_id);

          if($this->active_custom_tab instanceof HomeScreenTab) {
            if($this->active_custom_tab->getUserId() != $this->logged_user->getId()) {
              $this->response->forbidden(); // Trying to access somebody else's home screen tab
            } // if
          } else {
            $this->response->notFound(); // Home screen tab not found
          } // if
        } else {
          $this->response->notFound(); // No home screen tab ID provided
        } // if
      } // if

      $this->prepareTabs($this->logged_user, 'homescreen_tab_' . $this->active_custom_tab->getId());
      $this->response->assign('active_custom_tab', $this->active_custom_tab);
    } // custom_tab

    // ---------------------------------------------------
    //  Legacy
    // ---------------------------------------------------
    
    /**
     * Render global iCalendar feed
     */
    function ical() {
      $this->response->notFound();

//      if ($this->logged_user->isFeedUser()) {
//        $filter = $this->logged_user->projects()->getVisibleTypesFilter(Project::STATUS_ACTIVE, get_completable_project_object_types());
//        if($filter) {
//          $objects = ProjectObjects::find(array(
//            'conditions' => array($filter . ' AND completed_on IS NULL AND state >= ? AND visibility >= ?', STATE_VISIBLE, $this->logged_user->getMinVisibility()),
//            'order' => 'priority DESC',
//          ));
//          render_icalendar(lang('Global Calendar'), $objects, true);
//          die();
//        } else {
//          $this->response->notFound();
//        } // if
//      } else {
//        $this->response->forbidden();
//      } //if
    } // ical
    
    /**
     * Show iCalendar subscribe page
     */
    function ical_subscribe() {
      $this->response->notFound();

//      if ($this->logged_user->isFeedUser()) {
//        $this->wireframe->hidePrintButton();
//        $feed_token  = $this->logged_user->getFeedToken();
//
//        $ical_url = Router::assemble('ical',array('auth_api_token' => $feed_token));
//        $ical_subscribe_url = str_replace(array('http://', 'https://'), array('webcal://', 'webcal://'), $ical_url);
//
//        $this->response->assign(array(
//          'ical_url' => $ical_url,
//          'ical_subscribe_url' => $ical_subscribe_url
//        ));
//      } else {
//        $this->response->forbidden();
//      } // if
    } // ical_subscribe
    
  }