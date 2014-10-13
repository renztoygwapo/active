<?php

  /**
   * Modification logs framework definition
   *
   * @package angie.frameworks.history
   */
  class HistoryFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'history';
    
		/**
     * Define module routes
     */
    function defineRoutes() {
      Router::map('object_history', 'object-history', array('controller' => 'object_history', 'action' => 'index'));
    } // defineRoutes
    
  }