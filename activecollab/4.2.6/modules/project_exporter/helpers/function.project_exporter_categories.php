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
  
  function smarty_function_project_exporter_categories($params, $template) {
  	$current_category = array_var($params, 'category', null);
  	$categories = array_var($params, 'categories', null);
  	$type = strtolower(array_var($params, 'type', null));
  	
  	if (!is_foreachable($categories)) {
  		return false;
  	} // if
  	
  	mysql_data_seek($categories->getResource(), 0);
  	
  	$return = '<div id="object_details"><dl class="properties"><dt>' . lang('Category') . ':</dt><dd><ul class="category_list">';
  	if (!$current_category) {
  		$return.= '<li class="selected">';
  	} else {
  		$return.= '<li>';
  	} // if
  	$return .= '<a href="' . $template->tpl_vars['url_prefix']->value . $type .  '/index.html">' . lang('All Categories') . '</a>, </li>';
  	
  	foreach ($categories as $category) {
  		if ($current_category && ($category->getId() == $current_category->getId())) {
  			$return.= '<li class="selected">';
  		} else {
  			$return.= '<li>';
  		} // if
  		$return .= '<a href="' . $template->tpl_vars['url_prefix']->value . $type .  '/category_' . $category->getId() . '.html">' . clean($category->getName()) . '</a>, </li>';
  	} // foreach
  	$return.= '</ul></dd></dl></div>';
  	
  	return $return;
  } // smarty_function_project_exporter_categories
  
     
          