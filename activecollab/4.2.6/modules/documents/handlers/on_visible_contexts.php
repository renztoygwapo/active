<?php

  /**
   * on_visible_contexts event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_visible_contexts event
   *
   * @param IUser $user
   * @param array $contexts
   * @param array $ignore_contexts
   * @param ApplicationObject $in
   * @param array $include_domains
   */
  function documents_handle_on_visible_contexts(IUser &$user, &$contexts, &$ignore_contexts, $in, $include_domains) {
    if($user instanceof User && empty($in) && ($include_domains === null || in_array('documents', $include_domains))) {
      Documents::getContextsByUser($user, $contexts, $ignore_contexts);
    } // if
  } // documents_handle_on_visible_contexts