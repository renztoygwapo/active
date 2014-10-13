<?php

  /**
   * Project object subtask label
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class AssignmentLabel extends FwAssignmentLabel {
  	
  	/**
     * Return always_uppercase
     *
     * @return boolean
     */
    function getAlwaysUppercase() {
    	return $this->always_uppercase;
    } //getAlwaysUppercase
    
  }