<?php

  /**
   * on_milestone_sections event handler implementation
   *
   * @package activeCollab.modules.discussions
   * @subpackage handlers
   */

  /**
   * Populate discussions information for milestone details page
   *
   * @param Project $project
   * @param Milestone $milestone
   * @param User $user
   * @param NamedList $sections
   * @param string $interface
   */
  function discussions_handle_on_milestone_sections(&$project, &$milestone, &$user, &$sections, $interface) {
    if(Discussions::canAccess($user, $project) && array_key_exists('discussions', $project->getTabs($user,$interface)->toArray())) {
  		$section = array(
        'text' => lang('Discussions'),
        'url' => Router::assemble('milestone_discussions', array('project_slug' => $project->getSlug(), 'milestone_id' => $milestone->getId())),
        'options' => array(),
      );
      
      $sections->add('discussions', $section);
  	} // if
  } // discussions_handle_on_milestone_sections