<?php

  /**
   * project_exporter_project_overview helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Shows an overview of the project
   *
   * Parameters:
   * 
   * - project - instance of Project
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_project_overview($params, $template) {
  	$project = array_var($params, 'project', null);
  	if (!($project instanceof Project)) {   
			throw new InvalidInstanceError('project', $project, 'Project');  		
  	} // if
  	
  	AngieApplication::useHelper('date', GLOBALIZATION_FRAMEWORK, 'modifier');
  	
  	$return = '<dl class="properties">';
  	
  	$user_permalink = 'people/user_' . $project->getCreatedById() . '.html';
    $return.= '<dt>' . lang('Created By') . ':</dt>';
    $return.= '<dd><div class="created_by content"><a href="' . $user_permalink . '">' . clean($project->getCreatedByName()) . '</a></div></dd>';
    
    $return.= '<dt>' . lang('Created On') . ':</dt>';
    $return.= '<dd><div class="created_on content">' . smarty_modifier_date($project->getCreatedOn()) . '</div></dd>';
    
    $return.= '<dt>' . lang('Name') . ':</dt>';
    $return.= '<dd><div class="name content">' . clean($project->getName()) . '</div></dd>';
    
    $leader_permalink = 'people/user_' . $project->getLeaderId() . '.html';
    $return.= '<dt>' . lang('Leader') . ':</dt>';
    $return.= '<dd><div class="leader content"><a href="' . $leader_permalink . '">' . clean($project->getLeaderName()) . '</a></div></dd>';
    
    $completed = $project->complete()->isCompleted() ? lang('Completed') : lang('Active');
    $return.= '<dt>' . lang('Status') . ':</dt>';
    $return.= '<dd><div class="status content">' . $completed . '</div></dd>';
    
    $return.= '<dt>' . lang('Details') . ':</dt>';
    $return.= '<dd><div class="details content">' . HTML::toRichText($project->getOverview()) . '</div></dd>';
    
    $return.= '</dl>';
	
	return $return;
  } // smarty_function_project_exporter_milestone_list