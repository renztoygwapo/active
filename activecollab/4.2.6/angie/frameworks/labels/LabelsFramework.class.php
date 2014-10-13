<?php

  /**
   * Labels framework definition
   *
   * @package angie.frameworks.labels
   */
  class LabelsFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'labels';
    
    /**
     * Define labels admin routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param array $context_defaults
     * @param array $context_requirements
     */
    static function defineLabelsAdminRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $label_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('label_id' => Router::MATCH_ID)) : array('label_id' => Router::MATCH_ID);
      
      Router::map("{$context}_labels", "$context_path/labels", array('controller' => $controller_name, 'action' => "{$context}_labels", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_labels_add", "$context_path/labels/add", array('controller' => $controller_name, 'action' => "{$context}_add_label", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_label", "$context_path/labels/:label_id", array('controller' => $controller_name, 'action' => "{$context}_view_label", 'module' => $module_name), $label_requirements);
      Router::map("{$context}_label_edit", "$context_path/labels/:label_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_label", 'module' => $module_name), $label_requirements);
      Router::map("{$context}_label_delete", "$context_path/labels/:label_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_label", 'module' => $module_name), $label_requirements);
      Router::map("{$context}_label_set_as_default", "$context_path/labels/:label_id/set-as-default", array('controller' => $controller_name, 'action' => "{$context}_set_label_as_default", 'module' => $module_name), $label_requirements);
    } // defineLabelsAdminRoutesFor
    
  	/**
     * Define label routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param array $context_defaults
     * @param array $context_requirements
     */
    function defineLabelsRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {      
      Router::map("{$context}_update_label", "$context_path/update-label", array('controller' => $controller_name, 'action' => "{$context}_update_label", 'module' => $module_name));
    } // defineLabelsRoutesFor
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
    } // defineHandlers
    
  }