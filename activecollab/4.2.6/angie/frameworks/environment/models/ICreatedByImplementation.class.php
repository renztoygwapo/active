<?php

  /**
   * Created by implementation
   *
   * @package angie.framework.environment
   * @subpackage models
   */
  class ICreatedByImplementation {
    
    /**
     * Instance of parent object
     *
     * @var ICreatedBy
     */
    protected $object;
    
    /**
     * Cached created by instance
     *
     * @var User
     */
    private $created_by = false;
    
    /**
     * Construct created by implementation
     *
     * @param ICreatedBy $object
     */
    function __construct(ICreatedBy $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Set created by instance
     *
     * @return User
     */
    function get() {
      if($this->created_by === false) {
        $created_by_id = $this->object->getCreatedById();
        
        if($created_by_id) {
          $this->created_by = Users::findById($created_by_id);
        } // if
        
        if(!($this->created_by instanceof User)) {
          $this->created_by = new AnonymousUser($this->object->getCreatedByName(), $this->object->getCreatedByEmail());
        } // if
      } // if
      return $this->created_by;
    } // get
    
    /**
     * Set instance of user who created parent object
     *
     * @param User $created_by
     */
    function set($created_by) {
      if($created_by === null) {
        $this->object->setCreatedById(0);
        $this->object->setCreatedByName('');
        $this->object->setCreatedByEmail('');
      } elseif($created_by instanceof User) {
        $this->object->setCreatedById($created_by->getId());
        $this->object->setCreatedByName($created_by->getDisplayName());
        $this->object->setCreatedByEmail($created_by->getEmail());
      } elseif($created_by instanceof AnonymousUser) {
        $this->object->setCreatedById(0);
        $this->object->setCreatedByName($created_by->getName());
        $this->object->setCreatedByEmail($created_by->getEmail());
      } // if
      
      $this->created_by = $created_by;
    } // set
    
  }

?>