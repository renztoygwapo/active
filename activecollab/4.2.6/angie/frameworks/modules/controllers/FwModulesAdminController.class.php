<?php

  // Build on top of administration controller
  AngieApplication::useController('admin', MODULES_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level modules administration controller implementation
   *
   * @package angie.frameworks.modules
   * @subpackage controllers
   */
  class FwModulesAdminController extends AdminController {
    
    /**
     * Selected module
     *
     * @var AngieModule
     */
    protected $active_module;
  
    /**
     * Prepare controller
     */
    function __before() {
      parent::__before();

      if(!MODULES_MANAGEMENT_ENABLED) {
        $this->response->notFound();
      } // if
      
      $this->wireframe->breadcrumbs->add('modules_admin', lang('Modules'), Router::assemble('modules_admin'));
      
      $module_name = $this->request->get('module_name');
      if($module_name) {
        try {
          $this->active_module = AngieApplication::getModule($module_name, false);
        } catch(InvalidParamError $e) {
          $this->response->notFound();
        } catch(Exception $e) {
          throw $e;
        } // try
        
        $this->wireframe->breadcrumbs->add('module', $this->active_module->getDisplayName(), $this->active_module->getViewUrl());
        
        if($this->request->getAction() == 'module') {
          $this->wireframe->setPageObject($this->active_module, $this->logged_user);
        } // if
      } // if
      
      $this->smarty->assign('active_module', $this->active_module);
    } // __construct
    
    /**
     * Show modules administration index page
     */
    function index() {
      $native_modules = $custom_modules = array();

      foreach(AngieApplication::getAllModules() as $module_name => $module) {
        if($module->isNative()) {
          $native_modules[$module_name] = $module;
        } else {
          $custom_modules[$module_name] = $module;
        } //if
      } //foreach

      $this->smarty->assign(array(
        'application_name' => AngieApplication::getName(),
        'application_version' => AngieApplication::getVersion(),
        'modules' => array(
          'native_modules' => $native_modules,
          'custom_modules' => $custom_modules
        ),
        'disable_custom_modules_url' => Router::assemble('disable_custom_modules')
      ));
    } // index
    
    /**
     * Show specific module details
     */
    function module() {
      
    } // module
    
    /**
     * Install module
     */
    function install() {
      try {
        $log = array();
        
        $init_steps = new NamedList();
        
        $init_steps->add('checks', array(
          'text' => lang('Check availability'),
          'url' => Router::assemble('execute_installation_steps', array('what' => 'check', 'module_name' => $this->active_module->getName())), 
         ));
        
         $init_steps->add('install_module', array(
          'text' => lang('Installing module'),
          'url' => Router::assemble('execute_installation_steps', array('what' => 'install_module', 'module_name' => $this->active_module->getName())), 
         ));
         
         $init_steps->add('done', array(
          'text' => lang('Finishing'),
          'url' => Router::assemble('execute_installation_steps', array('what' => 'done', 'module_name' => $this->active_module->getName())), 
         ));
         
        $this->response->assign(array(
          'installation_steps' => $init_steps,
          'can_be_installed' => $this->active_module->canBeInstalled($log),
          'installation_check_log' => $log,
          'active_module' => $this->active_module
        ));
      } catch(Error $e) {
        $this->response->exception($e);
      } // try
    } // install
    
    /**
     * Execute installation steps
     */
    function execute_installation_steps() {
      try {
        $action = $this->request->get('what');
        switch ($action) {

          // Pre-installation check
          case 'check':
           $log = array();
           $this->active_module->canBeInstalled($log);
            
           if(count($log)) {
             throw new Error(lang('Module validation failed'));
           }//if
           
           $this->response->ok();
           break;

          // Install module
          case 'install_module':
            if($this->active_module->canInstall($this->logged_user)) {
              $this->active_module->install();
              $this->active_module->postInstall($this->logged_user);

              AngieApplication::rebuildLocalization();
              clean_menu_projects_and_quick_add_cache();

              $this->response->ok();
            } else {
              throw new Error(lang('You do not have permission to install this module'));
            } // if

            break;

          // Done, module has been installed
          case 'done':
           $this->response->respondWithData($this->active_module->describe($this->logged_user));
           break;
        }//switch
      } catch (Error $e) {
        $this->response->exception($e);
      }//try
    }//execute_installation_steps
    
    /**
     * Uninstall
     */
    function uninstall() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(!$this->active_module->isInstalled(false)) {
          $this->response->notFound();
        } // if
        
        if(!$this->active_module->canUninstall($this->logged_user)) {
          $this->response->forbidden();
        } // if
        
        try {
          $this->active_module->uninstall();
          AngieApplication::rebuildLocalization();
          clean_menu_projects_and_quick_add_cache();
          $this->response->respondWithData($this->active_module->getName(), array(
            'as' => 'module'
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // uninstall
    
    /**
     * Enable selected module
     */
    function enable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(!$this->active_module->canEnable($this->logged_user)) {
          $this->response->forbidden();
        } // if
        
        try {
          $this->active_module->enable();
          clean_menu_projects_and_quick_add_cache();
          $this->response->respondWithData($this->active_module->describe($this->logged_user), array('as' => 'module'));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // enable
    
    /**
     * Disable selected module
     */
    function disable() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        if(!$this->active_module->canDisable($this->logged_user)) {
          $this->response->forbidden();
        } // if
        
        try {
          $this->active_module->disable();
          clean_menu_projects_and_quick_add_cache();
          $this->response->respondWithData($this->active_module->describe($this->logged_user), array('as' => 'module'));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // disable

    /**
     * Disable custom modules
     */
    function disable_custom_modules() {
      if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
        $modules = AngieApplication::getAllModules();
        foreach($modules as $module) {
          if(!$module->isNative() && $module->canDisable($this->logged_user)) {
            try {
              $module->disable();
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } // if
        } // foreach
        $this->response->ok();
      } else {
        $this->response->badRequest();
      } // if
    } // disable_custom_modules
    
  }