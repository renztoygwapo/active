<?php

  /**
   * Download framework definition
   *
   * @package angie.frameworks.download
   */
  class DownloadFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'download';
    
    /**
     * Define attachment routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function defineDownloadRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map("{$context}_download", "$context_path/download", array('controller' => $controller_name, 'action' => "{$context}_download_content", 'module' => $module_name), $context_requirements);
    } // defineDownloadRoutesFor
    
  }

?>