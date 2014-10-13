<?php

  /**
   * Base Discussion Inspector implementation
   * 
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class IDiscussionInspectorImplementation extends IProjectObjectInspectorImplementation {
    
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