<?php

  /**
   * Base Notebook Page Inspector implementation
   * 
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookPageInspectorImplementation extends IInspectorImplementation {
    
    /**
     * Load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    public function load(IUser $user, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->getRenderScope() == IInspectorImplementation::RENDER_SCOPE_QUICK_VIEW) {
        $this->addProperty('project', lang('Project'), new ProjectInspectorProperty($this->object));
        $this->addProperty('notebook', lang('Notebook'), new NotebookInspectorProperty($this->object));
      } // if

      parent::load($user, $interface);

      $this->supports_body = false;
      $this->optional_body_content = '<p class="empty_page">' . lang('This page has no content') . '</p>';
    } // load
    
  }