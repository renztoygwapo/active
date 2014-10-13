<?php

  /**
   * Company users context implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ICompanyUsersContextImplementation extends IUsersContextImplementation {
    
    /**
     * Construct company users helper implementation
     *
     * @param Company $object
     * @throws InvalidInstanceError
     */
    function __construct(Company $object) {
      if($object instanceof Company) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Company');
      } // if
    } // __construct
    
    /**
     * Return true if $user is member of this users context
     *
     * @param User $user
     * @param boolean $use_cache
     * @return boolean
     */
    function isMember(User $user, $use_cache = true) {
      return $user->getCompanyId() == $this->object->getId();
    } // isMember
    
    /**
     * Return users in given context
     *
     * @param User $user
     * @param integer $min_state
     * @return User[]
     * @throws InvalidInstanceError
     */
    function get($user = null, $min_state = STATE_VISIBLE) {
      if($user instanceof User) {
        return Users::findByCompany($this->object, $user->visibleUserIds($this->object, $min_state), $min_state);
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // get
    
    /**
     * Return users for select box
     *
     * @param User $user
     * @param mixed $exclude_ids
     * @param integer $min_state
     * @return array
     */
    function getForSelect(User $user, $exclude_ids = null, $min_state = STATE_VISIBLE) {
      $users_table = TABLE_PREFIX . 'users';
      
      if($exclude_ids) {
        return Users::getForSelectByConditions(array("$users_table.id IN (?) AND $users_table.id NOT IN (?) AND $users_table.company_id = ?", $user->visibleUserIds(null, $min_state), $exclude_ids, $this->object->getId()));
      } else {
        return Users::getForSelectByConditions(array("$users_table.id IN (?) AND $users_table.company_id = ?", $user->visibleUserIds(null, $min_state), $this->object->getId()));
      } // if
    } // getForSelect
    
    /**
     * Return user ID-s in this context
     *
     * @param User $user
     * @return array
     * @throws InvalidInstanceError
     */
    function getIds($user = null) {
      if($user instanceof User) {
        return Users::findUserIdsByCompany($this->object, $user->visibleUserIds());
      } else {
        throw new InvalidInstanceError('user', $user, 'User');
      } // if
    } // getIds

    /**
     * Add user to this context
     *
     * @param User $user
     * @param bool $save
     * @return User
     * @throws Exception
     */
    function add(User $user, $save = true) {
      try {
        DB::beginWork('Adding user to a company @ ' . __CLASS__);
        
        $user->setCompanyId($this->object->getId());
        if($save) {
          $user->save();
        } // if
        
        DB::commit('User added to a company @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to add user to a company @ ' . __CLASS__);
        throw $e;
      } // try
      
      return $user;
    } // add
    
    /**
     * Remove user from this context
     *
     * @param User $user
     * @param User $by
     * @throws NotImplementedError
     */
    function remove(User $user, User $by) {
      throw new NotImplementedError(__CLASS__ . '::' . __METHOD__, 'User - Company relation is not optional');
    } // remove

    /**
     * Clear all relations
     *
     * @param User $user
     * @throws NotImplementedError
     */
    function clear(User $user) {
      throw new NotImplementedError(__METHOD__);
    } // clear
    
    /**
     * Replace one user with another user
     *
     * @param User $replace
     * @param User $with
     * @throws NotImplementedError
     */
    function replace(User $replace, User $with) {
      throw new NotImplementedError(__METHOD__);
    } // replace
    
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
      $company_users = $this->get($user);

      if($company_users) {
        $result = array();

        foreach($company_users as $company_user) {
          $result[] = $company_user->describe($user, false, $for_interface);
        } // foreach

        return $result;
      } // if

      return null;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      $company_users = $this->get($user);

      if($company_users) {
        $result = array();

        foreach($company_users as $company_user) {
          $result[] = $company_user->describeForApi($user);
        } // foreach

        return $result;
      } // if

      return null;
    } // describeForApi
    
  }