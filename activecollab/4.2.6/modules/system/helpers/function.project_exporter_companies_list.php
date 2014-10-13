<?php

  /**
   * project_exporter_companies_list helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Shows a list of companies
   *
   * Parameters:
   * 
   * - project - instance of Project
   * - people - array of User
   * - companies - array if Company
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_companies_list($params, $template) {
  	$project = array_var($params, 'project', null);
  	$people = array_var($params, 'people', null);
  	$companies = array_var($params, 'companies', null);
  	if (!($project instanceof Project)) {   
	  throw new InvalidInstanceError('project', $project, 'Project');  		
  	} // if
  	
  	$return = '';
	  
    if(is_foreachable($people)) {
      foreach($companies as $company) {
        $company_permalink = $template->tpl_vars['url_prefix']->value . 'people/company_' . $company->getId() . '.html';
        $return .= '<div class="company">';
        $return .= '<div class="company_name"><a href="'. $company_permalink . '">' . clean($company->getName()) . '</a></div>';
        $return .= '<div class="company_container">';
        $return .= '<table class="common" id="users_list">';
        foreach ($people[$company->getId()] as $user) {
          $user_permalink = $template->tpl_vars['url_prefix']->value . 'people/user_' . $user->getId() . '.html';
      	  $return .= '<tr>';
    	  $return .= '<td class="column_thumbnail"><a href="' . $user_permalink .'">'.$template->tpl_vars['exporter']->value->storeAvatar('user_'.$user->getId().'.png',$user->avatar()->getUrl(),true).'</a></td>';
      	  $return .= '<td class="column_name"><a href="' . $user_permalink . '">' . clean($user->getName()) . '</a></td>';
    	  $return .= '<td class="column_email"><a href="mailto:' . clean($user->getEmail()) . '">' . clean($user->getEmail()) . '</a></td>';
    	  $return .= '<td class="column_role">' . clean($user->projects()->getRoleName($project)) . '</td>';
    	  $return .= '</tr>';
        } //foreach
        $return .= '</table></div></div>';
      } // foreach
    } //if
    
	return $return;
  } // smarty_function_project_exporter_companies_list