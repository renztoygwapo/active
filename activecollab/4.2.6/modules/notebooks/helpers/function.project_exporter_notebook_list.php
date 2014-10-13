<?php

  /**
   * project_exporter_notebook_list helper
   *
   * @package activeCollab.modules.notebooks
   * @subpackage helpers
   */
  
  /**
   * Shows a list of notebooks
   *
   * Parameters:
   * 
   * - project - instance of Project
   * 
   * - milestone - instance of Milestone
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_notebook_list($params, $template) {
  	if (!((boolean) DB::executeFirstCell('SELECT COUNT(name) FROM ' . TABLE_PREFIX . 'modules WHERE name = ?', NOTEBOOKS_MODULE))) {
  		return '';
  	} //if
  	$project = array_var($params, 'project', null);
  	if (!($project instanceof Project)) {   
  	  throw new InvalidInstanceError('project', $project, 'Project');
  	} // if
  	
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$milestone = array_var($params, 'milestone', null);
  	$visibility = array_var($params, 'visibility', $template->tpl_vars['visibility']->value);
    $navigation_sections = array_var($params, 'navigation_sections', null);

    $return = '<div id="milestone_notebooks" class="object_info">';
  	
  	if ($milestone instanceof Milestone) {
  	  $notebooks = Notebooks::find(array(
        'conditions' => array('project_id = ? AND type = ? AND milestone_id = ? AND state >= ? AND visibility >= ?', $project->getId(), 'Notebook', $milestone->getId(), STATE_ARCHIVED, $visibility),
        'order' => 'ISNULL(position) ASC, position ASC'
      ));
      if (is_foreachable($notebooks)) {
        $return .= '<h3>' . lang('Notebooks') . '</h3>';
      } else {
        return '';
      } //if
  	} else {
  	  $notebooks = Notebooks::findByProject($project, STATE_ARCHIVED, $visibility);
  	}//if
  	
  	if (!is_foreachable($notebooks)) {
  		return '<p>' . lang('There are no notebooks in this project') . '</p>'; 
  	} // if
  	
  	$return .= '<table class="common" id="notebooks_list">';
  	
  	foreach ($notebooks as $notebook) {
  	  if ($notebook instanceof Notebook) {
        if (!$navigation_sections || ($navigation_sections && array_key_exists('notebooks', $navigation_sections))) {
          $permalink = 'href="' . $template->tpl_vars['url_prefix']->value . 'notebooks/notebook_' . $notebook->getId() . '.html"';
        } else {
          //$permalink = 'href="javascript:void(0)" onclick="alert(\'' . lang('Notebook section is not implemented in this exported project') . '\')"';
          continue;
        } //if
        
  	    $return .= '<tr>';
		    $return .= '<td class="column_thumbnail"><a ' . $permalink .'>'.$template->tpl_vars['exporter']->value->storeAvatar('notebook_'.$notebook->getId().'.png',$notebook->avatar()->getUrl(145),true).'</a></td>';
  	    $return .= '<td class="column_id"><a ' . $permalink . '>' . $notebook->getId() . '</a></td>';
		    $return .= '<td class="column_name"><a ' . $permalink . '>' . clean($notebook->getName()) . '</a></td>';
		    $return .= '<td class="column_date">' . smarty_modifier_date($notebook->getCreatedOn()) . '</td>';
		    $return .= '<td class="column_author">' . smarty_function_project_exporter_user_link(array('id' => $notebook->getCreatedById(), 'name' => $notebook->getCreatedByName(), 'email' => $notebook->getCreatedByEmail()), $template) . '</td>';
	      $return .= '</tr>';
  	  } //if
  	} //foreach
  	$return.= '</table></div>';
	
	return $return;
  } //