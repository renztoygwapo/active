<?php

  // Build on top of backend controller
  AngieApplication::useController('backend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level reports controller implementation
   * 
   * @package angie.frameworks.reports
   * @subpackage controllers
   */
  abstract class FwReportsController extends BackendController {

    /**
     * Indicator whether this controller should check user's access permissions
     *
     * @var bool
     */
    protected $check_reports_access_permissions = true;
  
    /**
     * Execute before any action
     */
    function __before() {
      parent::__before();

      if($this->check_reports_access_permissions && !$this->logged_user->canUseReports()) {
        $this->response->forbidden();
      } // if
      
      $this->wireframe->tabs->clear();
      $this->wireframe->tabs->add('reports', lang('Reports and Filters'), Router::assemble('reports'), null, true);
      
      EventsManager::trigger('on_reports_tabs', array(&$this->wireframe->tabs, &$this->logged_user));
      
      $this->wireframe->breadcrumbs->add('reports', lang('Reports and Filters'), Router::assemble('reports'));
      $this->wireframe->hidePrintButton();
      
      $this->wireframe->setCurrentMenuItem('reports');
    } // __before
    
    /**
     * Display administration index page
     */
    function index() {
      $reports_panel = new ReportsPanel();
      
      EventsManager::trigger('on_reports_panel', array(&$reports_panel, &$this->logged_user));
      
      $this->smarty->assign('reports_panel', $reports_panel);
    } // index
    
  }