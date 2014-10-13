<?php

  /**
   * Data Sources module initialization file
   * 
   * @package angie.frameworks.data_sources
   */
  
  const DATA_SOURCES_FRAMEWORK = 'data_sources';
  const DATA_SOURCES_FRAMEWORK_PATH = __DIR__;
  
  AngieApplication::setForAutoload(array(
    'FwDataSource' => DATA_SOURCES_FRAMEWORK_PATH . '/models/data_sources/FwDataSource.class.php',
    'FwDataSources' => DATA_SOURCES_FRAMEWORK_PATH . '/models/data_sources/FwDataSources.class.php',

    'FwDataSourceMapping' => DATA_SOURCES_FRAMEWORK_PATH . '/models/data_source_mappings/FwDataSourceMapping.class.php',
    'FwDataSourceMappings' => DATA_SOURCES_FRAMEWORK_PATH . '/models/data_source_mappings/FwDataSourceMappings.class.php',

    'Basecamp' => DATA_SOURCES_FRAMEWORK_PATH . '/models/basecamp/Basecamp.class.php',
    'BasecampSource' => DATA_SOURCES_FRAMEWORK_PATH . '/models/basecamp/BasecampSource.class.php',
  ));