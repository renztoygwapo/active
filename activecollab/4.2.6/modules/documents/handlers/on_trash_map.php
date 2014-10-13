<?php

  /**
   * on_trash_map event handler
   *
   * @package activeCollab.modules.documents
   * @subpackage handlers
   */

  /**
   * Handle on_trash_map event
   *
   * @param NamedList $sections
   * @param array $map
   * @param User $user
   */
  function documents_handle_on_trash_map(&$map, User &$user) {
    
  	$map = array_merge(
  		(array) $map,
  		(array) Documents::getTrashedMap($user)
  	); 
    
  } // documents_handle_on_trash_map