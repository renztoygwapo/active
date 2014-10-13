<?php

  /**
   * on_visible_contexts event handler
   * 
   * @package activeCollab.modules.system
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
  function system_handle_on_visible_contexts(IUser &$user, &$contexts, &$ignore_contexts, $in, $include_domains) {
    if($user instanceof User) {
      
      // People contexts
      if((empty($in) || $in instanceof Company) && ($include_domains === null || in_array('people', $include_domains))) {
        if($in instanceof Company) {
          Companies::getContextsByUser($user, $contexts, $ignore_contexts, array($in->getId()));
        } else {
          Companies::getContextsByUser($user, $contexts, $ignore_contexts);
        } // if
      } // if
      
      // User contexts
      
      // Project contexts
      if((empty($in) || $in instanceof Project) && ($include_domains === null || in_array('projects', $include_domains))) {
        if($in instanceof Project) { 
          Projects::getContextsByUser($user, $contexts, $ignore_contexts, array($in->getId()));
        } else { 
          Projects::getContextsByUser($user, $contexts, $ignore_contexts);
        } // if
      } // if
    } // if
  } // system_handle_on_visible_contexts