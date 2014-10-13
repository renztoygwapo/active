<?php

  /**
   * Search framework model definition
   *
   * @package angie.frameworks.Search
   * @subpackage resources
   */
  class SearchFrameworkModel extends AngieFrameworkModel {
    
    /**
     * Load initial search framework data
     * 
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      $this->addConfigOption('search_provider', 'MySqlSearchProvider');
      $this->addConfigOption('search_initialized_on');

      $this->addConfigOption('elastic_search_hosts', 'localhost:9200');

      parent::loadInitialData($environment);
    } // loadInitialData
    
  }