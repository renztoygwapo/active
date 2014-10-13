<?php

  /**
   * on_project_created event handler
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage handlers
   */

  /**
   * Handle on_project_created event
   *
   * @param Project $project
   * @param User $user
   */
  function invoicing_handle_on_project_created(Project &$project, User &$user) {
    $project_based_on = $project->getBasedOn();

    if($project_based_on instanceof Quote) {
      $project_based_on->copyComments($project, $user);

      $template = $project->getTemplate();

      if(!($template instanceof ProjectTemplate)) {
        $project_based_on->createMilestones($project, $user);
      } // if
    } // if
  } // invoicing_handle_on_project_created