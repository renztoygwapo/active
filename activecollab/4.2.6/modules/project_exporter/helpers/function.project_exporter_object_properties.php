<?php

  /**
   * project_exporter_object_properties helper
   *
   * @package activeCollab.modules.project_exporter
   * @subpackage helpers
   */
  
  /**
   * Show a object properties
   *
   * Parameters:
   * 
   * - object - Object of which properties are shown
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_object_properties($params, $template) {
    AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$return ='';
  	
  	$object = array_var($params, 'object');
  	if (!($object instanceof ProjectObject)) {
  		throw new InvalidInstanceError('object', $object, 'ProjectObject');
  	} // if
  	  	  	
  	$return.= '<dl class="properties">';
  	// created by
  	if ($object->getCreatedByEmail() || $object->getCreatedByName() || $object->getCreatedById()) {
			$return.= '<dt>' . lang('Created by') . ':</dt>';
			$return.= '<dd>' . smarty_function_project_exporter_user_link(array('id' => $object->getCreatedById(), 'name' => $object->getCreatedByName(), 'email' => $object->getCreatedByEmail()), $template) . '</dd>';
  	} // if
  	
  	// created on
  	if ($object->getCreatedOn() instanceof DateValue) {
			$return.= '<dt>' . lang('Created on') . ':</dt>';
			$return.= '<dd>' . smarty_modifier_date($object->getCreatedOn()) . '</dd>';
  	} // if
  	 	
  	// completed on
  	if (($object instanceof IComplete) && ($object->getCompletedOn() instanceof DateValue)) {
			$return.= '<dt>' . lang('Completed by') . ':</dt>';			
			$return.= '<dd>' . smarty_function_project_exporter_user_link(array('id' => $object->getCompletedById(), 'name' => $object->getCompletedByName(), 'email' => $object->getCompletedByEmail()), $template) . '</dd>';
				
			$return.= '<dt>' . lang('Completed on') . ':</dt>';
			$return.= '<dd>' . smarty_modifier_date($object->getCompletedOn()) . '</dd>';
  	} // if
    	 
  	if ($object instanceof File) {
			$return.= '<dt>' . lang('File Details') . ':</dt>';
			$return.= '<dd>' . format_file_size($object->getSize()) . ' (' . clean($object->getMimeType()) . ')</dd>';
  	} // if

    if ($object instanceof Bookmark) {
      $return.= '<dt>' . lang('Bookmark URL') . ':</dt>';
      $return.= '<dd>' . '<a href = "' . $object->getBookmarkUrl() . '" target="_blank">' . $object->getBookmarkUrl() . '</a>' . '</dd>';
    } // if

  	if ($object instanceof ICategory && $object->getCategoryId()) {
  		$return.= '<dt>' . lang('Category') . ':</dt>';
  		$return.= '<dd>' . smarty_function_project_exporter_category_link(array('id' => $object->getCategoryId(), 'type' => $object->getBaseTypeName()), $template) . '</dd>';  		
  	} // if  	

    // milestone
    if (array_key_exists('milestones', $template->tpl_vars['navigation_sections']->value) && $object->fieldExists('milestone_id') && $object->getMilestoneId()) {
      $return.= '<dt>' . lang('Milestone') . ':</dt>';
      $return.= '<dd>' . smarty_function_project_exporter_object_link(array('id' => $object->getMilestoneId()), $template) . '</dd>';
    } //if

  	// dueOn
  	if ($object->fieldExists('start_on') && ($object->getStartOn() instanceof DateValue)) {
  		$return.= '<dt>' . lang('From / To') . ':</dt>';
  		$return.= '<dd>' . smarty_modifier_date($object->getStartOn(), 0) . ' &mdash; ' . smarty_modifier_date($object->getDueOn(), 0) . '</dd>'; 
  	} else if ($object->getDueOn() instanceof DateValue) {
  		$return.= '<dt>' . lang('Due on') . ':</dt>';
  		$return.= '<dd>' . smarty_modifier_date($object->getDueOn(), 0) . '</dd>';
  	} // if
  	  	
    $return.= '<dt>' . lang('Priority') . ':</dt>';
    $return.= '<dd>' . clean($object->getFormattedPriority()) . '</dd>';
    
    if ($object->getBody()) {
    	$return.= '<dt>' . lang('Details') . ':</dt>';
    	$return.= '<dd><div class="body content">' . HTML::toRichText($object->getBody()) . '</div></dd>';
    } // if
    
    if ($object instanceof IAttachments) {
    	$attachments = smarty_function_project_exporter_object_attachments(array('object' => $object), $template);
    	if ($attachments) {
    		$return.= '<dt>' . lang('Attachments') . ':</dt>';
    		$return.= '<dd>' . $attachments . '</dd>';
    	} // if
    } // if
		
  	$return.= '</dl>';
  	return $return;
  } // smarty_function_project_exporter_object_properties