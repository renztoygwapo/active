<?php

  /**
   * on_empty_trash event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_empty_trash event
   *
   * @param NamedList $sections
   * @param User $user
   */
  function documents_handle_on_empty_trash(User &$user) {

    // delete trashed documents
    Documents::deleteTrashed($user);
    
  } // documents_handle_on_empty_trash