<?php

  /**
   * Preview framework definition
   *
   * @package angie.frameworks.preview
   */
  class PreviewFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'preview';

    /**
     * Define attachment routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param array $context_requirements
     */
    function definePreviewRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      Router::map('disk_space_remove_thumbnails', 'admin/disk-space/tools/remove-thumbnails', array('controller' => 'preview_disk_space_admin', 'action' => 'remove_thumbnails', 'module' => PREVIEW_FRAMEWORK_INJECT_INTO));

      Router::map("{$context}_preview", "$context_path/preview", array('controller' => $controller_name, 'action' => "{$context}_preview_content", 'module' => $module_name), $context_requirements);
    } // defineDownloadRoutesFor

    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_used_disk_space', 'on_used_disk_space');
    } // defineHandlers
    
  }

?>