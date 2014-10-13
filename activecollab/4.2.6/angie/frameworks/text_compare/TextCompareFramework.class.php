<?php

  /**
   * Text compare framework
   *
   * @package angie.frameworks.text_compare
   */
  class TextCompareFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'text_compare';
    
    /**
     * Define framework routes
     */
    function defineRoutes() {
      Router::map('compare_text', '/compare-text', array('controller' => 'text_compare', 'action' => 'compare_text', 'module' => TEXT_COMPARE_FRAMEWORK_INJECT_INTO));
      Router::map('compare_versions', '/compare-versions', array('controller' => 'text_compare', 'action' => 'compare_versions', 'module' => TEXT_COMPARE_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
  }