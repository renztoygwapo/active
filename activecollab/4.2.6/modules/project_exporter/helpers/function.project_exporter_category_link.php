<?php

  /**
   * project_exporter_category_link helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a category link
   *
   * Parameters:
   * 
   * - id - id of the category
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_category_link($params, $template) {
    $category_id = array_var($params, 'id', null);
    $category_type = array_var($params, 'type', null);
    $category = ProjectExporterStorage::getCategory($category_id);
    
    if ($category instanceof FwCategory) {
    	return '<a href="' . $template->tpl_vars['url_prefix']->value . Inflector::pluralize($category_type) . '/category_' . $category->getId() . '.html">' . clean($category->getName()) . '</a>';
    } // if
  } // smarty_function_project_exporter_category_link