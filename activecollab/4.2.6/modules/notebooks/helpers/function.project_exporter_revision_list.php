<?php

  /**
   * project_exporter_revision_list helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Shows a list of revisions
   *
   * Parameters:
   * 
   * - page - instance of NotebookPage
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_revision_list($params, $template) {
  	$page = array_var($params, 'page', null);
  	if (!($page instanceof NotebookPage)) {
  	  throw new InvalidInstanceError('page', $page, 'NotebookPage');
  	} // if
    $revisions = array_var($params, 'revisions', null);

  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');

  	if (!is_foreachable($revisions)) {
  		return '<p>' . lang('There are no other revisions in this page') . '</p>';
  	} // if
  	
  	$return = '<table class="common" id="revision_list">';
  	
  	foreach ($revisions as $revision) {
  	  if ($revision instanceof NotebookPageVersion) {
  	    $permalink = $template->tpl_vars['url_prefix']->value . 'notebooks/page_' . $page->getId() . '_' . $revision->getVersion() . '.html';
  	    $return .= '<tr>';
  	    $return .= '<td class="column_revision"><a href="' . $permalink . '">#' . $revision->getVersion() . '</a></td>';
		    $return .= '<td class="column_date">' . smarty_modifier_date($revision->getCreatedOn()) . '</td>';
		    $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $revision->getCreatedById(), 'name' => $revision->getCreatedByName(), 'email' => $revision->getCreatedByEmail()), $template) . '</td>';
	      $return .= '</tr>';
  	  } //if
  	} //foreach
  	$return.= '</table>';
	
	return $return;
  } //smarty_function_project_exporter_revision_list