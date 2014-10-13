<?php

  // Build on top of project controller
  AngieApplication::useController('project', SYSTEM_MODULE);

  /**
   * Project activity logs controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectActivityLogsController extends ProjectController {
  
    /**
     * List project activities
     */
    function index() {
      
    } // index
    
    /**
     * Project activities as RSS feed
     */
    function rss() {
      
    } // rss
    
  }