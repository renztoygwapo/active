<?php

  /**
   * Document Preview implementation
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class IDocumentPreviewImplementation extends IDownloadPreviewImplementation {
    /**
     * Get preview url
     *
     * @return String
     */
    function getPreviewUrl() {
      return $this->object->getViewUrl();
    } // getPreviewUr
  } // IDocumentPreviewImplementation
  
?>