<?php

  /**
   * Project request attachments helper implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectRequestAttachmentsImplementation extends IAttachmentsImplementation {
    
    /**
     * Create a new attachment instance
     *
     * @return ProjectRequestAttachment
     */
    function newAttachment() {
      return new ProjectRequestAttachment();
    } // newAttachment
    
  }