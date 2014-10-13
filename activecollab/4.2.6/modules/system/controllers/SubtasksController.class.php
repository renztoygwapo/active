<?php

  // Build on top of subtasks framework
  AngieApplication::useController('fw_subtasks', SUBTASKS_FRAMEWORK);

  /**
   * Subtasks controller delegate
   *
   * @package activeCollab.modules.system
   * @subpackage controllers
   */
  class SubtasksController extends FwSubtasksController {
    
    /**
     * Object tracking controller delegate
     *
     * @var ObjectTrackingController
     */
    protected $object_tracking_delegate;
    
    /**
     * Schedule delegate controller
     * 
     * @var ScheduleController
     */
    protected $schedule_delegate;

    /**
     * Labels delegate controller
     *
     * @var LabelsController
     */
    protected $labels_delegate;
    
    /**
     * Construct controller
     *
     * @param Request $parent
     * @param mixed $context
     */
    function __construct($parent, $context = null) {
      parent::__construct($parent, $context);
      
      if($this->getControllerName() == 'subtasks') {
      	if(AngieApplication::isModuleLoaded('system')) {
      		$this->schedule_delegate = $this->__delegate('schedule', SYSTEM_MODULE, "{$context}_subtask");
      	}  // if
      	
      	if(AngieApplication::isModuleLoaded('tracking')) {
        	$this->object_tracking_delegate = $this->__delegate('object_tracking', TRACKING_MODULE, "{$context}_subtask");
      	} // if

        if (AngieApplication::isFrameworkLoaded('labels')) {
          $this->labels_delegate = $this->__delegate('labels', LABELS_FRAMEWORK_INJECT_INTO, "{$context}_subtask");
        } // if
      } // if
    } // __construct
    
    /**
     * Do before executing action
     */
    function __before() {
      parent::__before();
      
      if($this->schedule_delegate instanceof ScheduleController) {
      	$this->schedule_delegate->__setProperties(array(
          'active_object' => &$this->active_subtask,
        ));
      } // if
      
      if($this->object_tracking_delegate instanceof ObjectTrackingController) {
        $this->object_tracking_delegate->__setProperties(array(
          'active_tracking_object' => &$this->active_subtask,
        ));
      } // if

      if ($this->labels_delegate instanceof LabelsController) {
        $this->labels_delegate->__setProperties(array(
          'active_object' => &$this->active_subtask,
        ));
      } // if
    } // __before
    
  }