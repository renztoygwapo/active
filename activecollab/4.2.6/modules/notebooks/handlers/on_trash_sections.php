<?php

  /**
   * on_trash_sections event handler
   *
   * @package activeCollab.modules.notebooks
   * @subpackage handlers
   */

  /**
   * Handle on_trash_sections event
   *
   * @param NamedList $sections
   * @param array $map
   * @param User $user
   */
  function notebooks_handle_on_trash_sections(NamedList &$sections, &$map, User &$user) {
    $trashed_notebook_pages = NotebookPages::findTrashed($user, $map);

    if ($trashed_notebook_pages) {
      $sections->add('notebook_pages', array(
        'label' => lang('Notebook Pages'),
        'count' => count($trashed_notebook_pages),
        'items' => $trashed_notebook_pages
      ));
    } // if
  } // notebooks_handle_on_trash_sections