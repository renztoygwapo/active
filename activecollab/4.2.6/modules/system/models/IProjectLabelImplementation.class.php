<?php

  /**
   * Project labels implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectLabelImplementation extends ILabelImplementation {
  
    /**
     * Return new label instance for this specific implementation
     *
     * @return ProjectLabel
     */
    function newLabel() {
      return new ProjectLabel();
    } // newLabel
    
  }