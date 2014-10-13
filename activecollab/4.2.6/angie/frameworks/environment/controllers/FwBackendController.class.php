<?php

  /**
   * Framework level backend controller
   * 
   * @package angie.frameworks.environment
   * @subpackage controllers
   */
  class FwBackendController extends ApplicationController {

    /**
     * Activity logs delegate
     *
     * @var ActivityLogsController
     */
    protected $activity_logs_delegate;

    /**
     * Construct backend controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct(Request $parent, $context = null) {
      parent::__construct($parent, $context);

      if($this->getControllerName() == 'backend') {
        $this->activity_logs_delegate = $this->__delegate('activity_logs', ACTIVITY_LOGS_FRAMEWORK_INJECT_INTO, 'backend');
      } // if
    } // __construct

    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();

      $this->wireframe->setCurrentMenuItem('homepage');
    } // __before

    /**
     * Show backend home screen
     */
    function index() {
      if($this->request->isApiCall()) {
        $this->response->notFound(); // Don't show anything
      } // if

      // Rebuild indexes
      if($this->logged_user->isAdministrator() && ConfigOptions::getValue('require_index_rebuild', false)) {
        $this->wireframe->tabs->add('require_index_rebuild', lang('Rebuild Indexes'), Router::assemble('homepage'), null, true);

        $this->setView(array(
          'module' => ENVIRONMENT_FRAMEWORK,
          'controller' => null,
          'view' => '_require_index_rebuild',
        ));

        // Expired password
      } elseif($this->logged_user->isPasswordExpired()) {
        $this->wireframe->tabs->add('require_password_change', lang('Reset Password'), Router::assemble('homepage'), null, true);

        $this->setView(array(
          'module' => AUTHENTICATION_FRAMEWORK,
          'controller' => null,
          'view' => '_require_password_change',
        ));

      // Dashboard
      } else {
        $homescreen_tabs = $this->logged_user->homescreen()->getTabs();

        foreach($homescreen_tabs as $homescreen_tab) {
          if($homescreen_tab->isLoaded()) {
            $this->wireframe->tabs->add('homescreen_tab_' . $homescreen_tab->getId(), $homescreen_tab->getName(), $homescreen_tab->getHomescreenTabUrl());
          } else {
            $this->wireframe->tabs->add('homescreen_tab_dashboard', $homescreen_tab->getName(), $homescreen_tab->getHomescreenTabUrl());
          } // if
        } // foreach

        if($this->logged_user->homescreen()->canHaveOwn()) {
          $this->wireframe->tabs->addIcon('configure_homescreen', lang('Configure Home Screen'), $this->logged_user->homescreen()->getManageUrl(), AngieApplication::getImageUrl('icons/12x12/configure.png', HOMESCREENS_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT));
        } // if

        $homescreen_tab_id = $this->request->get('homescreen_tab_id');

        if(empty($homescreen_tab_id)) {
          $homescreen_tab_id = ConfigOptions::getValueFor('default_homescreen_tab_id', $this->logged_user);
        } // if

        if(!$homescreen_tab_id || $homescreen_tab_id == 'dashboard') {
          $active_homescreen_tab = first($homescreen_tabs);
        } else {
          $active_homescreen_tab = HomescreenTabs::findById((integer) $homescreen_tab_id);
        } // if

        if($active_homescreen_tab instanceof HomescreenTab) {
          if (!$active_homescreen_tab instanceof UserRoleDashboard && !($active_homescreen_tab->getUserId() == $this->logged_user->getId())) {
            $this->response->forbidden();
          } // if

          if($active_homescreen_tab->isLoaded()) {
            $this->wireframe->tabs->setCurrentTab('homescreen_tab_' . $active_homescreen_tab->getId());
          } else {
            $this->wireframe->tabs->setCurrentTab('homescreen_tab_dashboard');
          } // if

          $this->response->assign('active_homescreen_tab', $active_homescreen_tab);
        } else {
          $this->response->notFound();
        } // if
      } // if
    } // index
    
    /**
     * Get wireframe updates
     */
    function wireframe_updates() {
      if($this->request->isSubmitted()) {
        $wireframe_data = $this->request->post('data');
        if(!is_array($wireframe_data)) {
          $wireframe_data = array();
        } // if

        $on_unload = (boolean) array_var($wireframe_data, 'on_unload', false, true);
        
        $response_data = array();

        if(empty($on_unload)) {
          $response_data['status_bar_badges'] = array();
          $response_data['menu_bar_badges'] = array();
        } // if
        
        EventsManager::trigger('on_wireframe_updates', array(&$wireframe_data, &$response_data, $on_unload, &$this->logged_user));

        if($on_unload) {
          $response_data = 'on_unload';
        } // if

        $this->response->respondWithData($response_data, array(
          'format' => FORMAT_JSON,
        ));
      } else {
        $this->response->badRequest();
      } // if
    } // wireframe_updates

    /**
     * Refresh menu
     */
    function refresh_menu() {
      if ($this->request->isAsyncCall()) {
        $items_to_refresh = $this->request->get('items', null);
        if ($items_to_refresh) {
          $items_to_refresh = explode(',', $items_to_refresh);

          $main_menu = new MainMenu();
          $main_menu->load($this->logged_user, true, $items_to_refresh);
          $this->response->respondWithData($main_menu->toArray());
        } else {
          $this->response->ok();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // refresh_menu

    /**
     * Quick add action
     */
    function quick_add() {
      if($this->request->isAsyncCall() || $this->request->isMobileDevice()) {
        $return = $this->logged_user->getQuickAddData((AngieApplication::getPreferedInterface() ? AngieApplication::getPreferedInterface() : AngieApplication::INTERFACE_DEFAULT));

        // Respond to regular web browser
        if($this->request->isWebBrowser()) {
          $this->response->respondWithData($return);

          // Respond to phone device
        } elseif($this->request->isPhone()) {
          $this->wireframe->breadcrumbs->add('quick_add', lang('Quick Add'), Router::assemble('quick_add'));

          $projects = array();
          $companies = array();

          if(is_foreachable($return['subitems'])) {
            foreach($return['subitems'] as $key => $subitem) {
              if(substr($key, 0, 7) == 'project') {
                $projects[$key] = $subitem;
              } else {
                $companies[$key] = $subitem;
              } // if
            } // foreach
          } // if

          $this->response->assign('quick_add_data', array(
            'items' => $return['items'],
            'map' => $return['map'],
            'projects' => $projects,
            'companies' => $companies
          ));
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // quick_add
    
    /**
     * Return response interface
     * 
     * @return Response
     */
    protected function getResponseInstance() {
      if($this->request->isApiCall()) {
        return new ApiResponse($this, $this->request);
      } else {
        return new BackendWebInterfaceResponse($this, $this->request);
      } // if
    } // getResponseInstance

    /**
     * Return default layout
     */
    function getDefaultLayout() {
      return 'backend';
    } // getDefaultLayout
    
    /**
     * Return wireframe instance for this controller
     *
     * @return BackendWireframe
     */
    protected function getWireframeInstance() {
      if($this->request->isPhone()) {
        return new PhoneBackendWireframe($this->request);
      } elseif($this->request->isTablet()) {
        return new TabletBackendWireframe($this->request);
      } else {
        return new WebBrowserBackendWireframe($this->request);
      } // if
    } // getWireframe
  
  }