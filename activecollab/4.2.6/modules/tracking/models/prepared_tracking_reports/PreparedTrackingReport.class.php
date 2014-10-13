<?php

  /**
   * Default prepared report implementation
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  abstract class PreparedTrackingReport extends TrackingReport {
    
    /**
     * Make sure that prepared reports can't be modified
     *
     * @param string $name
     * @param mixed $value
     */
    function setAdditionalProperty($name, $value) {
      throw new NotImplementedError(__CLASS__ . '::' . __METHOD__);
    } // setAdditionalProperty
    
  }

?>