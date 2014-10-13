<?php

  /**
   * on_names_search_index_contexts events handler
   * 
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_names_search_index_contexts event
   * 
   * @param IUser $user
   * @param array $visible_contexts
   */
  function documents_handle_on_names_search_index_contexts(IUser &$user, &$visible_contexts) {
    if(Documents::canUse($user)) {
      $visible_contexts[] = 'documents/';
    } // if
  } // documents_handle_on_names_search_index_contexts