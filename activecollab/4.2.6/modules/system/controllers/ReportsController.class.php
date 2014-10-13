<?php

  // Build on top of framework level controller
  AngieApplication::useController('fw_reports', REPORTS_FRAMEWORK);

  /**
   * Application level reports controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ReportsController extends FwReportsController {
    
    /**
     * Show reports index page
     */
    function index() {
      $this->setView(array(
        'view' => 'index', 
        'controller' => 'fw_reports', 
        'module' => REPORTS_FRAMEWORK, 
      ));
      
      $reports_panel = new ReportsPanel();
      
      //$reports_panel->defineRow('people', new ReportsPanelRow(lang('People')));
      $reports_panel->defineRow('assignments', new ReportsPanelRow(lang('Assignments')));
      
      //$reports_panel->addTo('assignments', 'milestones', lang('Milestones'), Router::assemble('milestone_filters'), AngieApplication::getImageUrl('reports/milestones.png', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      $reports_panel->addTo('assignments', 'assignments', lang('Assignments'), Router::assemble('assignment_filters'), AngieApplication::getImageUrl('reports/assignments.png', SYSTEM_MODULE, AngieApplication::INTERFACE_DEFAULT));
      
      EventsManager::trigger('on_reports_panel', array(&$reports_panel, &$this->logged_user));
      
      $this->smarty->assign('reports_panel', $reports_panel);
    } // index
    
    /**
     * Display milestones report
     */
    function milestones() {
      
    } // milestones
    
  }