<?php

  /**
   * Categories framework implementation
   *
   * @package angie.framework.categories
   */
  class CategoriesFramework extends AngieFramework {
    
    /**
     * Short framework name
     *
     * @var string
     */
    protected $name = 'categories';
    
    /**
     * Define categories routs for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param mixed $context_requirements
     */
    function defineCategoriesRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
      $category_requirements = is_array($context_requirements) ? array_merge($context_requirements, array('category_id' => '\d+')) : array('category_id' => '\d+');
      
      Router::map("{$context}_categories", "$context_path/categories", array('controller' => $controller_name, 'action' => "{$context}_categories", 'module' => $module_name), $context_requirements);
      Router::map("{$context}_categories_add", "$context_path/categories/add", array('controller' => $controller_name, 'action' => "{$context}_add_category", 'module' => $module_name), $context_requirements);
      
      Router::map("{$context}_category", "$context_path/categories/:category_id", array('controller' => $controller_name, 'action' => "{$context}_view_category", 'module' => $module_name), $category_requirements);
      Router::map("{$context}_category_edit", "$context_path/categories/:category_id/edit", array('controller' => $controller_name, 'action' => "{$context}_edit_category", 'module' => $module_name), $category_requirements);
      Router::map("{$context}_category_delete", "$context_path/categories/:category_id/delete", array('controller' => $controller_name, 'action' => "{$context}_delete_category", 'module' => $module_name), $category_requirements);

      if (AngieApplication::isModuleLoaded('footprints')) {
        AngieApplication::getModule('footprints')->defineAccessLogRoutesFor("{$context}_category", "$context_path/categories/:category_id", $controller_name, $module_name, $category_requirements);
        AngieApplication::getModule('footprints')->defineHistoryOfChangesRoutesFor("{$context}_category", "$context_path/categories/:category_id", $controller_name, $module_name, $category_requirements);
      } // if
    } // defineCategoriesRoutesFor
    
    /**
     * Define category routes for given context
     *
     * @param string $context
     * @param string $context_path
     * @param string $controller_name
     * @param string $module_name
     * @param mixed $context_requirements
     */
    function defineCategoryRoutesFor($context, $context_path, $controller_name, $module_name, $context_requirements = null) {
			Router::map("{$context}_update_category", "$context_path/update-category", array('controller' => $controller_name, 'action' => "{$context}_update_category", 'module' => $module_name));
    } // defineCategoryRoutesFor
    
    /**
     * Define framework handlers
     */
    function defineHandlers() {
    } // defineHandlers
    
  }