<?php

  /**
   * Project Exporter module initialization file
   * 
   * @package activeCollab.modules.project_exporter
   */
  
  const PROJECT_EXPORTER_MODULE = 'project_exporter';
  const PROJECT_EXPORTER_MODULE_PATH = __DIR__;

  defined('PROJECT_EXPORTER_WORK_PATH') or define('PROJECT_EXPORTER_WORK_PATH', WORK_PATH . '/export');
  
  AngieApplication::setForAutoload(array(
  	'ProjectExporterStorage' => PROJECT_EXPORTER_MODULE_PATH . '/models/ProjectExporterStorage.class.php',
    'ProjectExporter' => PROJECT_EXPORTER_MODULE_PATH . '/models/ProjectExporter.class.php',
  ));