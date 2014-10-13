<?php

  /**
   * Avatar framework defintion class
   *
   * @package angie.frameworks.avatar
   */
  class AvatarFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'avatar';
    
    /**
     * Define avatar routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param array $context_defaults
     * @param array $context_requirements
     */
    function defineAvatarRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
    	Router::map("{$context}_avatar_view", "{$context_path}/avatar/view", array('controller' => $controller_name, 'action' => "{$context}/avatar_view", 'module' => $module_name), $context_requirements);
    	Router::map("{$context}_avatar_upload", "{$context_path}/avatar/upload", array('controller' => $controller_name, 'action' => "{$context}/avatar_upload", 'module' => $module_name), $context_requirements);
    	Router::map("{$context}_avatar_edit", "{$context_path}/avatar/edit", array('controller' => $controller_name, 'action' => "{$context}/avatar_edit", 'module' => $module_name), $context_requirements);
    	Router::map("{$context}_avatar_remove", "{$context_path}/avatar/remove", array('controller' => $controller_name, 'action' => "{$context}/avatar_remove", 'module' => $module_name), $context_requirements);
    } // defineSubtasksRoutesFor
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      
    } // defineHandlers
    
  }