<?php

  /**
   * Project objects specific comments implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class IProjectObjectCommentsImplementation extends ICommentsImplementation {
    
    /**
     * Construct project object subscriptions implementation
     *
     * @param IComments $object
     */
    function __construct(IComments $object) {
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