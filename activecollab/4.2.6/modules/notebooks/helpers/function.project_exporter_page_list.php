<?php

  /**
   * project_exporter_page_list helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Shows a list of pages
   *
   * Parameters:
   * 
   * - parent - mixed
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_project_exporter_page_list($params, $template) {
  	$parent = array_required_var($params, 'parent', true);
  	
  	if($parent instanceof Notebook || $parent instanceof NotebookPage) {
  	  $parent_type = get_class($parent);
  	  
  	  AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	  
    	$count_first_level = NotebookPages::count(array('parent_type = ? AND parent_id = ? AND state >= ?', $parent_type, $parent->getId(), STATE_ARCHIVED));
    	
    	if (!$count_first_level) {
    	  if($parent instanceof Notebook) {
    	    return '<p>' . lang('There are no pages in this notebook') . '</p>';
    	  } else {
    	    return '<p>' . lang('There are no subpages in this page') . '</p>';
    	  } // if
    	} //if
  	
    	return smarty_function_project_exporter_page_list_get_subpages($parent_type, $parent->getId(), $template);
  	} else {
  	  throw new InvalidInstanceError('parent', $parent, array('Notebook', 'NotebookPage'));
  	} // if
  } //smarty_function_project_exporter_page_list
  
  /**
   * Returns HTML table with subpages
   * 
   * @param string $parent_type
   * @param int $parent_id
   * @param string $template
   * @param int $level
   */
  function smarty_function_project_exporter_page_list_get_subpages($parent_type, $parent_id, $template, $level = -1) {
    $level++;
    if ($level === 0) {
      $return = '<table class="common" id="pages_list">';
    } //if
    
    $result = DB::execute("SELECT * FROM " . TABLE_PREFIX . "notebook_pages WHERE parent_type = ? AND parent_id = ? AND state >= ? ORDER BY ISNULL(position) ASC, position", $parent_type, $parent_id, STATE_ARCHIVED);
    
    if ($result instanceof DBResult) {
    	foreach ($result as $page) {
    		$permalink = $template->tpl_vars['url_prefix']->value . 'notebooks/page_' . $page['id'] . '.html';
	  	  $return .= '<tr>';
	  	  $return .= '<td class="column_id">';
	  	  if ($level > 0) {
	        for ($counter = 0; $counter < $level; $counter++) {
	          $return .= '<span class="tree_indent';
	          if ($counter == 0) {
	            $return .= ' first_tree_indent';
	          } elseif ($counter == ($level-1)) {
	            $return .= ' last_tree_indent';
	          } // if
	          $return .= '"></span>';
	        } //for
	  	  } //if
	  	  $return .= '<a href="' . $permalink . '">' . $page['id'] . '</a></td>';
	  	  $return .= '<td class="column_name"><a href="' . $permalink . '">' . clean($page['name']) . '</a></td>';
	  	  $return .= '<td class="column_version">v' . $page['version'] . '</td>';
	  	  $return .= '<td class="column_date">' . smarty_modifier_date($page['created_on']) . '</td>';                                 
	  	  $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $page['created_by_id'], 'name' => $page['created_by_name'], 'email' => $page['created_by_email']), $template) . '</td>';
	  	  $return .= '</tr>';
	  	  
	  	  $return .= smarty_function_project_exporter_page_list_get_subpages('NotebookPage', $page['id'], $template, $level);
    	} //foreach
    } else {
      return '';
    } //if
  	
    if ($level === 0) {
      $return.= '</table>';
    } //if
	
    return $return;
  } // smarty_function_project_exporter_page_list_get_subpages