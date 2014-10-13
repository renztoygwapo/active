<?php

  /**
   * Quote attachments helper implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IQuoteAttachmentsImplementation extends IAttachmentsImplementation {
    
    /**
     * Create a new attachment instance
     *
     * @return QuoteAttachment
     */
    function newAttachment() {
      return new QuoteAttachment();
    } // newAttachment
    
  }