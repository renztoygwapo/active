<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Project labels administration controller
   * 
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class ProjectLabelsAdminController extends AdminController {
  
    /**
     * Selected label
     *
     * @var ProjectLabel
     */
    protected $active_label;
    
    /**
     * Labels admin delegate controller
     * 
     * @var LabelsAdminController
     */
    protected $labels_admin_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'project_labels_admin') {
        $this->labels_admin_delegate = $this->__delegate('labels_admin', SYSTEM_MODULE, 'projects_admin');
      } // if
    } // __construct
    
    /**
     * Execute before any other controller action
     */
    function __before() {
      parent::__before();
      
      $label_id = $this->request->getId('label_id');
      if($label_id) {
        $this->active_label = Labels::findById($label_id);
      } // if
      
      if($this->active_label instanceof Label) {
        if($this->active_label instanceof ProjectLabel) {
          $this->wireframe->breadcrumbs->add('project_label', $this->active_label->getName(), $this->active_label->getViewUrl());
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->active_label = new ProjectLabel();
      } // if
      
      $this->response->assign('active_label', $this->active_label);
      
      if($this->labels_admin_delegate instanceof LabelsAdminController) {
      	$this->labels_admin_delegate->__setProperties(array(
      		'active_label' => &$this->active_label, 
      	  'label_type' => 'ProjectLabel', 
      	  'can_add_label' => Labels::canAdd($this->logged_user), 
      	  'labels_url' => Router::assemble('projects_admin_labels'), 
      	  'labels_add_url' => Router::assemble('projects_admin_labels_add'), 
      	));
      } // if
    } // __before
    
  }