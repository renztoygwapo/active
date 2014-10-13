<?php

  /**
   * Attachment class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class Attachment extends FwAttachment {
    
    /**
     * Give edit permissions to administrators and project managers
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      if($user->isAdministrator() || $user->isProjectManager()) {
        return true;
      } else {
        return parent::canEdit($user);
      } // if
    } // canEdit
    
  }