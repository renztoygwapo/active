<?php

  /**
   * Base Project Asset Inspector implementation
   * 
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IProjectAssetInspectorImplementation extends IProjectObjectInspectorImplementation {
    
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