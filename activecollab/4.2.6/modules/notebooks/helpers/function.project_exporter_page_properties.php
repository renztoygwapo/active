<?php

  /**
   * project_exporter_page_properties helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Show a page properties
   *
   * Parameters:
   * 
   * - NotebookPage - Page of which properties are shown
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_page_properties($params, $template) {
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$return ='';
  	
  	$page = array_var($params, 'object');
  	if (!($page instanceof NotebookPage)) {
  	  throw new InvalidInstanceError('page', $page, 'NotebookPage');
  	} // if
  	  	  	
  	$return.= '<dl class="properties">';
  	// created by
  	if ($page->getCreatedByEmail() || $page->getCreatedByName() || $page->getCreatedById()) {
	  $return.= '<dt>' . lang('Created by') . ':</dt>';
	  $return.= '<dd>' . smarty_function_project_exporter_user_link(array('id' => $page->getCreatedById(), 'name' => $page->getCreatedByName(), 'email' => $page->getCreatedByEmail()), $template) . '</dd>';
  	} // if
  	
  	// created on
  	if ($page->getCreatedOn() instanceof DateValue) {
	  $return.= '<dt>' . lang('Created on') . ':</dt>';
	  $return.= '<dd>' . smarty_modifier_date($page->getCreatedOn()) . '</dd>';
  	} // if
  	
  	//parent
  	$parent = $page->getParent();
  	if ($page->getParentType() === 'Notebook') {
  	  $return.= '<dt>' . lang('Notebook') . ':</dt>';
	  $return.= '<dd><a href="notebook_' . $parent->getId() . '.html">' . clean($parent->getName()) . '</a></dd>';
  	} elseif ($page->getParentType() === 'NotebookPage') {
  	  $return.= '<dt>' . lang('Parent Parent') . ':</dt>';
	  $return.= '<dd><a href="page_' . $parent->getId() . '.html">' . clean($parent->getName()) . '</a></dd>';
  	} //if
  	
   	$return.= '<dt>' . lang('Body') . ':</dt>';
    $return.= '<dd><div class="body content">' . HTML::toRichText($page->getBody()) . '</div></dd>';
    
    if ($page instanceof IAttachments) {
    	$attachments = smarty_function_project_exporter_object_attachments(array('object' => $page), $template);
    	if ($attachments) {
    		$return.= '<dt>' . lang('Attachments') . ':</dt>';
    		$return.= '<dd>' . $attachments . '</dd>';
    	} // if
    } // if
		
  	$return.= '</dl>';
  	return $return;
  } // smarty_function_project_exporter_object_properties