<?php

  /**
   * project_exporter_company_properties helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Shows company properties and the list of Users in that company and in that project
   *
   * Parameters:
   * 
   * - project - instance of Project
   * - company - instance of Company
   * - company_people - array of User
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_company_properties($params, $template) {
  	$company_people = array_var($params, 'company_people', null);
  	$company = array_var($params, 'company', null);
  	$project = array_var($params, 'project', null);
  	if (!($project instanceof Project)) {
  	  throw new InvalidInstanceError('project', $project, 'Project');
  	} // if
  	if (!($company instanceof Company)) {
  	  throw new InvalidInstanceError('company', $company, 'Company');
  	} // if
  	
  	$return = '';
	$return .= $template->tpl_vars['exporter']->value->storeAvatar('company_'.$company->getId().'.png',$company->avatar()->getUrl(),true);
    $return .= '<dl class="properties">';
    
    $address = ($company->getConfigValue('office_address')) ? $company->getConfigValue('office_address') : '----';
    $return.= '<dt>' . lang('Address') . ':</dt>';
    $return.= '<dd><div class="address content"><em>' . clean($address) . '</em></div></dd>';
    
    $phone = ($company->getConfigValue('office_phone')) ? $company->getConfigValue('office_phone') : '----';
    $return.= '<dt>' . lang('Phone Number') . ':</dt>';
    $return.= '<dd><div class="phone_number content"><em>' . clean($phone). '</em></div></dd>';
    
    $fax = $company->getConfigValue('office_fax');
    if ($fax) {
      $return.= '<dt>' . lang('Fax Number') . ':</dt>';
      $return.= '<dd><div class="fax_number content">' . clean($fax) . '</div></dd>';
    } //if
    
    $homepage = $company->getConfigValue('office_homepage');
    if (is_valid_url($homepage)) {
      $return.= '<dt>' . lang('Homepage') . ':</dt>';
      $return.= '<dd><div class="fax_number content"><a href="' . $homepage .'">' . $homepage . '</a></div></dd>';
    } //if
    
    $return.= '</dl>';
    // users in company
    $return .= '<table class="common" id="users_list">';
    foreach ($company_people as $user) {
      $user_permalink = $template->tpl_vars['url_prefix']->value . 'people/user_' . $user->getId() . '.html';
  	  $return .= '<tr>';
	  $return .= '<td class="column_thumbnail"><a href="' . $user_permalink .'">'.$template->tpl_vars['exporter']->value->storeAvatar('user_'.$user->getId().'.png',$user->avatar()->getUrl(),true).'</a></td>';
  	  $return .= '<td class="column_name"><a href="' . $user_permalink . '">' . clean($user->getName()) . '</a></td>';
	  $return .= '<td class="column_email"><a href="mailto:' . clean($user->getEmail()) . '">' . clean($user->getEmail()) . '</a></td>';
	  $return .= '<td class="column_role">' . clean($user->projects()->getRoleName($project)) . '</td>';
	  
	  $return .= '</tr>';
    } //foreach
    $return .= '</table>';
    
	return $return;
  } // smarty_function_project_exporter_company_properties