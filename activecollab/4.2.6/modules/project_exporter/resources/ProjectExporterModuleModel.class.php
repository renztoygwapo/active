<?php

  // Include application specific model base
  require_once APPLICATION_PATH . '/resources/ActiveCollabModuleModel.class.php';

  /**
   * Notebooks module model definition
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
	class ProjectExporterModuleModel extends ActiveCollabModuleModel {
    
    /**
     * Construct project exporter module model definition
     *
     * @param ProjectExporterModule $parent
     */
		function __construct(ProjectExporterModule $parent) {
      parent::__construct($parent);
    } // __construct
    
    /**
     * Load initial framework data
     *
     * @param string $environment
     */
    function loadInitialData($environment = null) {
      if(!file_exists(WORK_PATH . '/export')) {
        mkdir(WORK_PATH . '/export', 0777);
      } // if
    } // loadInitialData
    
  }