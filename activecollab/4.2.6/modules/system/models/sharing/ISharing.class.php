<?php

  /**
   * Sharing inteface defintion
   * 
   * @package activeCollab.modules.system
   * @subpackage sharing
   */
  interface ISharing {
  
    /**
     * Return sharing helper instance
     * 
     * @return ISharingImplementation
     */
    function sharing();
    
  }