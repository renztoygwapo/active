<?php

  /**
   * Base Invoice Inspector implementation
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IInvoiceInspectorImplementation extends IInspectorImplementation {

    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      parent::load($user, $interface);
      
      $this->supports_body = false;
    } // load
    
  }