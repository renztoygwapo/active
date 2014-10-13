<?php

  /**
   * System module on_project_overview_sidebars event handler
   *
   * @package activeCollab.modules.system
   * @subpackage handlers
   */
  
  /**
   * Add sidebars to project overview page
   *
   * @param array $sidebars
   * @param Project $project
   * @param User $user
   */
  function system_handle_on_project_overview_sidebars(&$sidebars, Project &$project, User &$user) {

    $can_see_last_activity = $project->isLeader($user) || $user->isProjectManager();

    if (MailToProjectInterceptor::isEnabled()) {
      $mail_to_project = lang('Add elements to this project by sending an email to <br/><a href="mailto::mail_to_project_email">:mail_to_project_text</a>', array(
        'mail_to_project_email' => $project->getMailToProjectEmail(),
        'mail_to_project_text' => $project->getMailToProjectEmail()
      ));

      $mail_to_project .= '<p><a href="' . $project->getMailToProjectLearnMoreUrl() . '" title="' . lang('Mail to Project') . '" class="link_button" id="mtp_link"><span class="inner">' . lang('Learn More') . '</span></a></p>';
      $mail_to_project .= '<script type="text/javascript">$("#mtp_link").flyout({"width" : "715"})</script>';

      $sidebars[] = array(
        'label' => lang('Send email to this project'),
        'is_important'  => false,
        'id' => 'mail_to_project',
        'body' => $mail_to_project,
      );
    } // if

    $project_users = $project->users()->get(Authentication::getLoggedUser(), STATE_VISIBLE);
    if (is_foreachable($project_users)) {
      AngieApplication::useHelper('user_link', AUTHENTICATION_FRAMEWORK);
      AngieApplication::useHelper('ago', GLOBALIZATION_FRAMEWORK, 'modifier');
      
      $output = '';
      $sorted_users = Users::groupByCompany($project_users);
      foreach ($sorted_users as $company_name => $users) {
        if(is_foreachable($users)) {
          $output.= '<h3>' . clean($company_name) . '</h3>';
          $output.= '<table class="company_users">';
          foreach ($users as $current_user) {
            $last_seen = '';
            if ($can_see_last_activity && ($user->getId() != $current_user->getId())) {
              if($current_user->getLastActivityOn() instanceof DateTimeValue) {
                $last_seen = smarty_modifier_ago($current_user->getLastActivityOn(), null, true);
              } else {
                $last_seen = lang('Not Logged in Yet');
              } // if
            } // if
          	$output.= '<tr><td class="icon_holder"><img src="'. $current_user->avatar()->getUrl(IUserAvatarImplementation::SIZE_SMALL) . '" /></td><td><a href="' . clean($current_user->getViewUrl()) . '" class="quick_view_item">' . clean($current_user->getDisplayName()) . '</a></td><td class="date">' . $last_seen. '</td></tr>';
          } // foreach
          $output.= '</table>';
        } // if
      } // foreach

      if ($project->canManagePeople($user)) {
        $output .= '<p class="center"><a href="' . $project->getPeopleUrl() . '">' . lang('Manage Project People') . '</a></p>';
      } // if
      
      $sidebars[] = array(
        'label' => lang('People on This Project'),
        'is_important'  => false,
        'id' => 'project_people',
        'body' => $output,
      );
    } // if
  } // system_handle_on_project_overview_sidebars