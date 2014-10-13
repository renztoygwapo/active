<?php

  /**
   * Base Notebook Page Inspector implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookInspectorImplementation extends IProjectObjectInspectorImplementation {
    
    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      parent::load($user, $interface);
      $this->supports_body = false;
      $this->optional_body_content = '<p class="empty_page">' . lang('This notebook has no description') . '</p>';
    } // load
      
    /**
     * do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);
    } // do_load
    
  }