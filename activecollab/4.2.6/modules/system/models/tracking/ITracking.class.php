<?php

  /**
   * Tracking interface
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  interface ITracking {
    
    /**
     * Return tracking implementation helper
     *
     * @return ITrackingImplementation
     */
    function tracking();
    
  }

?>