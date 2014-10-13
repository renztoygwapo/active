<?php

  /**
   * Framework level assignment label implementation
   * 
   * @package angie.frameworks.assignees
   * @subpackage models
   */
  abstract class FwAssignmentLabel extends Label {
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'assignments_admin_label';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('label_id' => $this->getId());
    } // getRoutingContextParams
    
    /**
     * Return even names prefix
     * 
     * @return string
     */
    function getEventNamesPrefix() {
      return 'assignment_label';
    } // getEventNamesPrefix
  
  }