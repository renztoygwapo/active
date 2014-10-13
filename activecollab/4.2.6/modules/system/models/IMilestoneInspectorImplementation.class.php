<?php

  /**
   * Base Milestone Inspector implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IMilestoneInspectorImplementation extends IProjectObjectInspectorImplementation {
    /**
     * do load data for given interface
     * 
     * @param IUser $user
     * @param string $interface
     */
    protected function do_load(IUser $user, $interface) {
      parent::do_load($user, $interface);
      
      // Assignees are property in phone interface
      if($interface == AngieApplication::INTERFACE_PHONE) {
      	$this->addProperty('assignees', lang('Assignees'), new AssigneesInspectorWidget($this->object, null));
      } elseif ($interface == AngieApplication::INTERFACE_PRINTER) {
      	$this->addProperty('assignees', lang('Assignees'), new AssigneesInspectorProperty($this->object));
      	$this->addProperty('milestone_progress', lang('Progress'), new SimpleFieldInspectorProperty($this->object, 'progress'));
      	//$this->addWidget('milestone_progressbar', lang('Milestone Progress'), new MilestoneProgressbarInspectorWidget($this->object));
      } else {
      	$this->addWidget('assignees', lang('Assignees'), new AssigneesInspectorWidget($this->object), null);
      	$this->addWidget('milestone_progressbar', lang('Milestone Progress'), new MilestoneProgressbarInspectorWidget($this->object));
      } // if
    } // do_load
    
  } // IMilestoneInspectorImplementation