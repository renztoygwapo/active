<?php

  /**
   * History interface
   *
   * @package angie.frameworks.history
   * @subpackage models
   */
  interface IHistory {
    
    /**
     * Return modification log implementation
     *
     * @return IHistoryImplementation
     */
    function history();
    
  }