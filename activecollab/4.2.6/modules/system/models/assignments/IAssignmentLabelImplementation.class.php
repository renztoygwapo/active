<?php

  /**
   * Project object subtask labels implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IAssignmentLabelImplementation extends ILabelImplementation {
    
    /**
     * Return new label instance for this specific implementation
     *
     * @return AssignmentLabel
     */
    function newLabel() {
      return new AssignmentLabel();
    } // newLabel
    
  }