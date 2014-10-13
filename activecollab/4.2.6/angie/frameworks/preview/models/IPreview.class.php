<?php

  /**
   * Preview interface
   *
   * @package angie.frameworks.preview
   * @subpackage models
   */
  interface IPreview {
    
    /**
     * Return preview helper
     *
     * @return IPreviewImplementation
     */
    function preview();
    
  }

?>