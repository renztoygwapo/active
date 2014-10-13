<?php

  /**
   * on_milestone_sections event handler implementation
   *
   * @package activeCollab.modules.todo
   * @subpackage handlers
   */

  /**
   * Populate notebook section information for milestone details page
   *
   * @param Project $project
   * @param Milestone $milestone
   * @param User $user
   * @param NamedList $sections
   * @param string $interface
   */
  function files_handle_on_milestone_sections(&$project, &$milestone, &$user, &$sections, $interface) {
  	if(ProjectAssets::canAccess($user, $project) && array_key_exists('files', $project->getTabs($user,$interface)->toArray())) {
  		$section = array(
        'text' => lang('Files'),
        'url' => Router::assemble('milestone_files', array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone->getId())),
        'options' => array(),
      );
      
      $sections->add('files', $section);
  	} // if
  } // todo_handle_on_milestone_sections