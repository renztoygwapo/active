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
  function notebooks_handle_on_milestone_sections(&$project, &$milestone, &$user, &$sections, $interface) {
  	if(Notebooks::canAccess($user, $project) && array_key_exists('notebooks', $project->getTabs($user,$interface)->toArray())) {
  		$section = array(
        'text' => lang('Notebooks'),
        'url' => Router::assemble('milestone_notebooks', array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone->getId())),
        'options' => array(),
      );
      
      $sections->add('notebooks', $section);
  	} // if
  } // todo_handle_on_milestone_sections