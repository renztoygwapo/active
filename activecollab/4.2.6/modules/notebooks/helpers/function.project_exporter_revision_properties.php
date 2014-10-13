<?php

  /**
   * project_exporter_revision_properties helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Show a revision properties
   *
   * Parameters:
   * 
   * - NotebookPageVersion - revision of which properties are shown
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_revision_properties($params, $template) {
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$return ='';
  	
  	$revision = array_var($params, 'object');
  	if (!($revision instanceof NotebookPageVersion)) {
  	  throw new InvalidInstanceError('revision', $revision, 'NotebookPageVersion');
  	} // if
    /**
     * @var NotebookPageVersion $revision
     */

    $return.= '<dl class="properties">';
  	// created by
  	if ($revision->getCreatedByEmail() || $revision->getCreatedByName() || $revision->getCreatedById()) {
	  $return.= '<dt>' . lang('Created by') . ':</dt>';
	  $return.= '<dd>' . smarty_function_project_exporter_user_link(array('id' => $revision->getCreatedById(), 'name' => $revision->getCreatedByName(), 'email' => $revision->getCreatedByEmail()), $template) . '</dd>';
  	} // if
  	
  	// created on
  	if ($revision->getCreatedOn() instanceof DateValue) {
	  $return.= '<dt>' . lang('Created on') . ':</dt>';
	  $return.= '<dd>' . smarty_modifier_date($revision->getCreatedOn()) . '</dd>';
  	} // if
  	
  	//current version
  	$return.= '<dt>' . lang('Current version') . ':</dt>';
	  $return.= '<dd><a href="page_'. $revision->getNotebookPageId() .'.html">' . $revision->getNotebookPage()->getName() . '</a></dd>';
  	
   	$return.= '<dt>' . lang('Body') . ':</dt>';
    $return.= '<dd><div class="body content">' . HTML::toRichText($revision->getBody()) . '</div></dd>';
    
  	$return.= '</dl>';
  	return $return;
  } // smarty_function_project_exporter_revision_properties