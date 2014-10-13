<?php

  /**
   * on_milestone_sections event handler implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage handlers
   */

  /**
   * Populate chekclist section information for milestone details page
   *
   * @param Project $project
   * @param Milestone $milestone
   * @param User $user
   * @param NamedList $sections
   * @param string $interface
   */
  function tasks_handle_on_milestone_sections(&$project, &$milestone, &$user, &$sections, $interface) {
  	if(Tasks::canAccess($user, $project) && array_key_exists('tasks', $project->getTabs($user,$interface)->toArray())) {
  		$section = array(
        'text' => lang('Tasks'),
        'url' => Router::assemble('milestone_tasks', array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone->getId())),
        'options' => array(),
      );
      
      $sections->add('tasks', $section);
  	} // if
  } // tasks_handle_on_milestone_sections