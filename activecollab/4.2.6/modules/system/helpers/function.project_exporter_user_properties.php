<?php

  /**
   * project_exporter_user_properties helper
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Shows user properties 
   *
   * Parameters:
   * 
   * - project - instance of Project
   * - user - instance of User
   * 
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  
  function smarty_function_project_exporter_user_properties($params, $template) {
  	$user = array_var($params, 'user', null);
  	$project = array_var($params, 'project', null);
  	if (!($project instanceof Project)) {
  	  throw new InvalidInstanceError('project', $project, 'Project');
  	} // if
  	if (!($user instanceof User)) {
  	  throw new InvalidInstanceError('user', $user, 'User');
  	} // if
  	$return = '';
	$return .= $template->tpl_vars['exporter']->value->storeAvatar('user_'.$user->getId().'.png',$user->avatar()->getUrl(),true);
    $return .= '<dl class="properties">';
    
    $company_premalink = $template->tpl_vars['url_prefix']->value . 'people/company_' . $user->getCompanyId() . '.html';
    $return.= '<dt>' . lang('Company') . ':</dt>';
    $return.= '<dd><div class="company content"><a href="' . $company_premalink . '">' . clean($user->getCompanyName()) . '</a></div></dd>';
    
    $system_role = lang($user->getRoleName());
    $return.= '<dt>' . lang('System Role') . ':</dt>';
    $return.= '<dd><div class="system_role content">' . $system_role . '</div></dd>';
    
    $return.= '<dt>' . lang('Project Role') . ':</dt>';
    $return.= '<dd><div class="project_role content">' . $user->projects()->getRoleName($project) . '</div></dd>';
    
    $return.= '<dt>' . lang('Email') . ':</dt>';
    $return.= '<dd><div class="email content"><a href="mailto:' . clean($user->getEmail()) . '">' . clean($user->getEmail()) . '</a></div></dd>';
    
    $phone_work = $user->getConfigValue('phone_work');
    if ($phone_work) {
      $return.= '<dt>' . lang('Work Phone Number') . ':</dt>';
      $return.= '<dd><div class="phone_work content">' . clean($phone_work) . '</div></dd>';
    } //if
    
    $phone_mobile = $user->getConfigValue('phone_mobile');
    if ($phone_mobile) {
      $return.= '<dt>' . lang('Mobile Phone Number') . ':</dt>';
      $return.= '<dd><div class="phone_mobile content">' . clean($phone_mobile) . '</div></dd>';
    } //if
    
    $im_type = $user->getConfigValue('im_type');
    $im_value = $user->getConfigValue('im_value');
    if ($im_type && $im_value) {
      $return.= '<dt>' . clean($im_type) . ':</dt>';
      $return.= '<dd><div class="im_value content">' . clean($im_value) . '</div></dd>';
    } //if
    
    $return.= '</dl>';
    
	return $return;
  } // smarty_function_project_exporter_companies_list