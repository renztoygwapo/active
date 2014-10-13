<?php

  /**
   * Project objects specific IComplete implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectObjectCompleteImplementation extends ICompleteImplementation {

    /**
     * Construct complete helper implementation
     *
     * @param IComplete $object
     */
    function __construct(IComplete $object) {
      if($object instanceof ProjectObject) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectObject');
      } // if
    } // __construct

    /**
     * Return notification subject prefix, so recipient can sort and filter notifications
     *
     * @return string
     */
    function getNotificationSubjectPrefix() {
      return $this->object->getProject() instanceof Project ? '[' . $this->object->getProject()->getName() . '] ' : '';
    } // getNotificationSubjectPrefix

  }