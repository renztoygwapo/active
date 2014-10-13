<?php

  /**
   * Project role
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ProjectRole extends BaseProjectRole implements IRoutingContext {
    
    // Defintiion of project role permission
    const PERMISSION_NONE = 0;
    const PERMISSION_ACCESS = 1;
    const PERMISSION_CREATE = 2;
    const PERMISSION_MANAGE = 3;
    
    /**
     * Bulk set object attributes
     *
     * @param array $attributes
     */
    function setAttributes($attributes) {
      if(isset($attributes['permissions'])) {
        $this->setPermissions($attributes['permissions']);
        unset($attributes['permissions']);
      } // if
      
      parent::setAttributes($attributes);
    } // setAttributes
    
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
      $result = parent::describe($user, $detailed, $for_interface);
      
      $result['role_permissions'] = array();
      
      foreach(array_keys(ProjectRoles::getPermissions()) as $permission) {
        $result['role_permissions'][$permission] = (integer) $this->getPermissionValue($permission, self::PERMISSION_NONE);
      } // foreach
      
      // Default project role, maybe
      $result['is_default'] = $this->getIsDefault();
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      
      return $result;
    } // describe

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
    function describeForApi(IUser $user, $detailed = false) {
      $result = parent::describeForApi($user, $detailed);

      $result['role_permissions'] = array();

      foreach(array_keys(ProjectRoles::getPermissions()) as $permission) {
        $result['role_permissions'][$permission] = (integer) $this->getPermissionValue($permission, self::PERMISSION_NONE);
      } // foreach

      // Default project role, maybe
      $result['is_default'] = $this->getIsDefault();
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();

      return $result;
    } // describeForApi
    
    /**
     * Prepare list of options that $user can use
     *
     * @param IUser $user
     * @param NamedList $options
     * @param string $interface
     * @return NamedList
     */
    protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($this->canEdit($user)) {
        $options->add('edit', array(
        	'text' => lang('Edit'),
          'url' => $this->getEditUrl(),
        	'icon' => AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK), 
          'onclick' => new FlyoutFormCallback(array(
            'success_event' => 'project_role_updated', 
          )), 
        ));
      } // if
      
      if($this->canDelete($user)) {
        $options->add('delete', array(
          'text' => lang('Delete'),
          'url' => $this->getDeleteUrl(),  
          'icon' => AngieApplication::getImageUrl('/icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK),
          'onclick' => new AsyncLinkCallback(array(
            'confirmation' => lang('Are you sure that you want to delete this role?'), 
            'success_event' => 'project_role_deleted', 
          )),
        ));
      } // if
    } // prepareOptionsFor
    
    // ---------------------------------------------------
    //  Users
    // ---------------------------------------------------
    
    /**
     * Return all users who use this role
     * 
     * @return DBResult
     */
    function getProjectUsers(IUser $user) {
      return DB::execute('SELECT * FROM ' . TABLE_PREFIX . 'project_users WHERE role_id = ?', $this->getId());
    } // getUsers
    
    /**
     * Return number of users who use this role
     *
     * @return integer
     */
    function countUsers() {
      return (integer) DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'project_users WHERE role_id = ?', $this->getId());
    } // countUsers
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Cached permissions array
     *
     * @var mixed
     */
    protected $permissions = false;
    
    /**
     * Return permissions
     *
     * @return array
     */
    function getPermissions() {
      return $this->permissions;
    } // getPermissions
    
    /**
     * Set permissions values
     *
     * @param mixed
     * @return mixed
     */
    function setPermissions($value) {
      $this->permissions = array();
      
      if(is_array($value)) {
        foreach($value as $k => $v) {
          $this->setPermissionValue($k, $v);
        } // foreach
      } // if
      
      return $this->permissions;
    } // setPermissions
    
    /**
     * Return permission value
     *
     * @param string $permission
     * @param mixed $default
     * @return mixed
     */
    function getPermissionValue($permission, $default = ProjectRole::PERMISSION_NONE) {
      return isset($this->permissions[$permission]) ? $this->permissions[$permission] : $default;
    } // getPermissionValue
    
    /**
     * Set permission value
     *
     * @param string $permission
     * @param boolean $value
     */
    function setPermissionValue($permission, $value) {
      $value = (integer) $value;
      
      if($value >= self::PERMISSION_NONE && $value <= self::PERMISSION_MANAGE) {
        $this->permissions[$permission] = $value;
      } else {
        throw new InvalidParamError('value', $value, "'$value' is not a valid project permission value");
      } // if
    } // setPermissionValue
    
    // ---------------------------------------------------
    //  Inteface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'admin_project_role';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('role_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can update this role
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can delete this role
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      if($user instanceof User) {
        return $user->isAdministrator() && $this->countUsers() < 1 && !$this->getIsDefault();;
      } else {
        return false;
      } // if
    } // canDelete
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Returns set as default URL
     *
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('admin_project_role_set_as_default', array('role_id' => $this->getId()));
    } // getSetAsDefaultUrl
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * When row is loaded, unserialize row permissions
     *
     * @param array $row
     * @param boolean $cache_row
     */
    function loadFromRow($row, $cache_row = false) {
      parent::loadFromRow($row, $cache_row);
      
      $raw = $this->getFieldValue('permissions');
      $this->permissions = empty($raw) ? array() : unserialize($raw);
      
      return true;
    } // loadFromRow
    
    /**
     * Save role to database
     */
    function save() {
      $this->setFieldValue('permissions', serialize($this->permissions));
      parent::save();
    } // save
    
    /**
     * Validate before save
     *
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name', 3)) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Role name needs to be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Role name is required and it needs to be at least 3 characters long'), 'name');
      } // if
    } // validate
    
  }