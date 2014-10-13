<?php

  /**
   * people_on_project helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage helpers
   */
  
  /**
   * Render project progress bar
   *
   * @param array $params
   * @param Smarty $smarty
   * @return string
   */
  function smarty_function_people_on_project($params, &$smarty) {
    $project = array_required_var($params, 'project',true,'Project');
    $user = array_required_var($params, 'user', true, 'IUser');
    
    // only project leader, system administrators and project manages can see last activity
    $can_see_last_activity = $project->isLeader($user) || $user->isAdministrator() || $user->isProjectManager();

    $project_users = $project->users()->get(Authentication::getLoggedUser());
    $interface = array_var($params, 'interface', AngieApplication::getPreferedInterface(), true);
     
    AngieApplication::useHelper('user_link', AUTHENTICATION_FRAMEWORK);
    AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');

    $output = '';

    // Main interface
    if($interface == AngieApplication::INTERFACE_DEFAULT && is_foreachable($project_users)) {
      $output = '<div class="people_on_project">';
      $sorted_users = Users::groupByCompany($project_users);
      foreach ($sorted_users as $company_name => $users) {
        if(is_foreachable($users)) {
          $output.= '<h3>' . clean($company_name) . '</h3>';
          $output.= '<table class="company_users">';
          foreach ($users as $current_user) {
            $last_seen = '';
            if ($can_see_last_activity && $current_user->getLastActivityOn() instanceof DateTimeValue && $user->getId() != $current_user->getId()) {
              $last_seen = smarty_modifier_ago($current_user->getLastActivityOn(), null, true);
            } // if
            $output.= '<tr><td class="icon_holder"><img src="'. $current_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL) . '" /></td><td>' . smarty_function_user_link(array('user' => $current_user), $smarty) . '</td><td class="date">' . $last_seen. '</td></tr>';
          } // foreach
          $output.= '</table>';
        } // if
      } // foreach
      $output .= '<p class="center"><a href="' . $project->getPeopleUrl() . '">' . lang('Manage Project People') . '</a></p>';

      $output .= '</div>';

    // Printer interface
    } else if($interface == AngieApplication::INTERFACE_PRINTER && is_foreachable($project_users)) {
      $output = '<div class="people_on_project">';
      $output .= '<h2>' . lang('People on this project') . '</h2>';
      $sorted_users = Users::groupByCompany($project_users);
      foreach ($sorted_users as $company_name => $users) {
        if(is_foreachable($users)) {
          $output.= '<h3>' . clean($company_name) . '</h3>';
          $output.= '<table class="company_users common">';
          foreach ($users as $current_user) {
            $last_seen = '';
            if ($current_user->getLastActivityOn() instanceof DateTimeValue && $can_see_last_activity && $user->getId() != $current_user->getId()) {
              $last_seen = $current_user->getLastActivityOn() instanceof DateTimeValue ? smarty_modifier_ago($current_user->getLastActivityOn(), null, true) : lang('Never');
            } // if
            $output.= '<tr><td class="icon_holder"><img src="'. $current_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL) . '" /></td>
                    <td>' . smarty_function_user_link(array('user' => $current_user), $smarty) . '</td>
                    <td class="date">' . $last_seen. '</td></tr>';
          } // foreach
          $output.= '</table>';
        } // if
      } // foreach

      $output .= '</div>';
    } // if
    
    return $output;
  } // smarty_function_project_progress