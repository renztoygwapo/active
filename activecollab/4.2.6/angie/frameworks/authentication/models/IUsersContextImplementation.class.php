<?php

  /**
   * Users context helper implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  abstract class IUsersContextImplementation implements IDescribe {
    
    /**
     * Parent object instance
     *
     * @var IUsersContext
     */
    protected $object;
    
    /**
     * Construct users implementation
     *
     * @param IUsersContext $object
     */
    function __construct(IUsersContext $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Count users
     * 
     * @param User $user
     * @return integer
     */
    function count(User $user) {
      return Users::count();
    } // count
    
    /**
     * Return true if $user is member of this users context
     *
     * @param User $user
     * @return boolean
     */
    abstract function isMember(User $user);
    
    /**
     * Return users in given context
     *
     * @param User $user
     * @param integer $min_state
     * @return User[]
     */
    function get($user = null, $min_state = STATE_VISIBLE) {
      return Users::find();
    } // get
    
    /**
     * Return users for select box
     *
     * @param User $user
     * @param array $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      return Users::getForSelect($user, $exclude_ids, $min_state);
    } // getForSelect
    
    /**
     * Add user to this context
     *
     * @param User $user
     */
    abstract function add(User $user);
    
    /**
     * Remove user from this context
     *
     * @param User $user
     * @param User $by
     */
    abstract function remove(User $user, User $by);
    
    /**
     * Clear all relations
     *
     * @param User $user
     */
    abstract function clear(User $user);
    
    /**
     * Replace one user with another user
     *
     * @param User $replace
     * @param User $with
     */
    function replace(User $replace, User $with) {
      $this->remove($replace, $with);
      $this->add($with);
    } // replace
    
  }