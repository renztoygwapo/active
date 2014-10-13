<?php

  /**
   * on_empty_trash event handler
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_empty_trash event
   *
   * @param NamedList $sections
   * @param User $user
   */
  function notebooks_handle_on_empty_trash(User &$user) {
    NotebookPages::deleteTrashed($user);
  } // notebooks_handle_on_empty_trash