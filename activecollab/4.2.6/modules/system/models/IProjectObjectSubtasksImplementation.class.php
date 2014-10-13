<?php

  /**
   * Project objects specific subtasks implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectSubtasksImplementation extends ISubtasksImplementation {
    
    /**
     * Construct project object subscriptions implementation
     *
     * @param ISubtasks $object
     */
    function __construct(ISubtasks $object) {
      if($object instanceof ProjectObject) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectObject');
      } // if
    } // __construct
    
    /**
     * Create new subtask instance
     *
     * @return ProjectObjectSubtask
     */
    function newSubtask() {
      $subtask = new ProjectObjectSubtask();
      $subtask->setParent($this->object);
      
      return $subtask;
    } // newSubtask

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return $this->object->getProject() instanceof Project ? '[' . $this->object->getProject()->getName() . '] ' : '';
    } // getNotificationSubjectPrefix
    
    /**
     * Return time records for all subtasks in given object
     * 
     * @param $user
     * @param $status
     */
    function getTimeRecords(IUser $user, $status = BILLABLE_STATUS_BILLABLE, $completed = ISubtasksImplementation::ANY) {
      $subtasks = $this->get($user,$completed);
      
      $total_records = array();
      if(is_foreachable($subtasks)) {
        foreach($subtasks as $subtask) {
          $records = $subtask->tracking()->getTimeRecords($user, $status);
          if($records instanceof MySQLDBResult) {
            $records = $records->toArray();
          } else {
            $records = array();
          }//if
          $total_records = array_merge($records,$total_records);
        }//foreach
      }//if
      return $total_records;
    }//getTimeRecords
    
    /**
     * Return expenses for all subtasks in given object
     * 
     * @param $user
     * @param $status
     */
    function getExpenses(IUser $user, $status = BILLABLE_STATUS_BILLABLE, $completed = ISubtasksImplementation::ANY) {
      $subtasks = $this->get($user,$completed);
      $total_expenses = array();
      if(is_foreachable($subtasks)) {
        foreach($subtasks as $subtask) {
          $records = $subtask->tracking()->getExpenses($user, $status);
          if($records instanceof MySQLDBResult) {
            $records = $records->toArray();
          } else {
            $records = array();
          }//if
          $total_expenses = array_merge($records,$total_expenses);
        }//foreach
      }//if
      return $total_expenses;
    }//getExpenses
    
  }