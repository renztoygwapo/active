<?php

  /**
   * on_trash_sections event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_trash_sections event
   *
   * @param NamedList $sections
   * @param array $map
   * @param User $user
   */
  function documents_handle_on_trash_sections(NamedList &$sections, &$map, User &$user) {
    
    // time records in trash
    $trashed_documents = Documents::findTrashed($user, $map);
    if (is_foreachable($trashed_documents)) {
      $sections->add('documents', array(
        'label' => lang('Documents'),
        'count' => count($trashed_documents),
        'items' => $trashed_documents
      ));
    } // if 
    
  } // documents_handle_on_trash_sections