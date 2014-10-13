<?php

  // Build on top of frontend controller
  AngieApplication::useController('frontend', SYSTEM_MODULE);

  /**
   * Public tasks controller
   * 
   * @package activeCollab.modules.tasks
   * @subpackage controllers
   */
  class PublicTasksController extends FrontendController {

    /**
     * Show index page
     */
    function index() {
      $this->smarty->assign('task_forms', PublicTaskForms::findEnabled());
    } // index
    
  }