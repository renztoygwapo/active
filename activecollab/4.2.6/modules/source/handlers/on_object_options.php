<?php

  /**
   * Source module on_object_options event handler
   *
   * @package activeCollab.modules.source
   * @subpackage handlers
   */

  /**
   * Source module on_object_options event handler
   *
   * @param ApplicationObject $object
   * @param IUser $user
   * @param NamedList $options
   * @param string $interface
   */
  function source_handle_on_object_options(&$object, &$user, &$options, $interface) {
    
    // Add a quick option which links to the list of commits related to the object
    if(($object instanceof Task || $object instanceof Discussion || $object instanceof Milestone || $object instanceof TodoList) && $object->canView($user)) {
      $commits = CommitProjectObjects::findCommitsByObject($object);

      $object_commits_count = is_array($commits) ? count($commits) : null;
      if ($object_commits_count) {
        $options->add('new_revision', array(
          'text' => lang('Commits (:object_commits)', array('object_commits' => $object_commits_count)),
          'url' => Router::assemble('repository_project_object_commits', array('project_slug' => $object->getProject()->getSlug(), 'object_id' => $object->getId())),
          'onclick' => new FlyoutCallback()
        ));
      } // if
    } // if
    
  } // source_handle_on_object_options