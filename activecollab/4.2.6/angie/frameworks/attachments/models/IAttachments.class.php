<?php

  /**
   * Definition of attachments interface
   *
   * @package angie.framework.attatchments
   * @subpackage models
   */
  interface IAttachments {
    
    /**
     * Return attachments implementation
     *
     * @return IAttachmentsImplementation
     */
    function &attachments();
    
  }