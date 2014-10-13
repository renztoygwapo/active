<?php

  // Build on top of admin controller
  AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Assignments label controller
   * 
   * @package angie.frameworks.assignees
   * @subpackage controllers
   */
  abstract class FwAssignmentLabelsAdminController extends AdminController {
    
    /**
     * Selected label
     *
     * @var AssignmentLabel
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
      
      if($this->getControllerName() == 'assignment_labels_admin') {
        $this->labels_admin_delegate = $this->__delegate('labels_admin', ASSIGNEES_FRAMEWORK_INJECT_INTO, 'assignments_admin');
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
        if($this->active_label instanceof AssignmentLabel) {
          $this->wireframe->breadcrumbs->add('assignment_label', $this->active_label->getName(), $this->active_label->getViewUrl());
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->active_label = new AssignmentLabel();
      } // if
      
      $this->response->assign('active_label', $this->active_label);
      
      if($this->labels_admin_delegate instanceof LabelsAdminController) {
      	$this->labels_admin_delegate->__setProperties(array(
      		'active_label' => &$this->active_label, 
      	  'label_type' => 'AssignmentLabel', 
      	  'can_add_label' => Labels::canAdd($this->logged_user), 
      	  'labels_url' => Router::assemble('assignments_admin_labels'), 
      	  'labels_add_url' => Router::assemble('assignments_admin_labels_add'), 
      	));
      } // if
    } // __before
  
  }