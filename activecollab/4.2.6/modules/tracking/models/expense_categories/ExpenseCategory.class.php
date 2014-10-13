<?php

  /**
   * ExpenseCategory class
   *
   * @package activeCollab.modules.tracking
   * @subpackage models
   */
  class ExpenseCategory extends BaseExpenseCategory implements IRoutingContext {
    
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
      
      $result['is_default'] = $this->getIsDefault();
      
      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      
      return $result;
    } // describe

    /**
     * Return array or property => value pairs that describes this object
     *
     * @param IUser $user
     * @param boolean $detailed
     * @return array
     */
    function describeForApi(IUser $user, $detailed = false) {
      return array(
        'id' => $this->getId(),
        'name' => $this->getName(),
        'is_default' => $this->getIsDefault(),
      );
    } // describeForApi
    
    // ---------------------------------------------------
    //  Interface implementations
    // ---------------------------------------------------
    
    /**
     * Return routing context name
     *
     * @return string
     */
    function getRoutingContext() {
      return 'expense_category';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('expense_category_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URLs
    // ---------------------------------------------------
    
    /**
     * Return set as default URL
     * 
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('expense_category_set_as_default', array(
      	'expense_category_id' => $this->getId()
      ));
    } // getSetAsDefaultUrl
    
    /**
     * Return true if this expense category is used for estimate
     * 
     * @return boolean
     */
    function isUsed() {
      return (boolean) Expenses::countByCategory($this);
    }//isUsed
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------
    
    /**
     * Returns true if $user can see details of this expense category
     * 
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $user->isAdministrator();
    } // canView
  
    /**
     * Return true if $user can update this expense category
     * 
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $user->isAdministrator();
    } // canEdit
    
    /**
     * Returns true if $user can set this expense category as default
     * 
     * @param User $user
     * @return boolean
     */
    function canSetAsDefault(User $user) {
      return $this->canEdit($user);
    } // canSetAsDefault
    
    /**
     * Return true if $user can delete this expense category
     * 
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $user->isAdministrator() && !($this->getIsDefault() || ExpenseCategories::count() <= 1 || $this->isUsed());
    } // canDelete
    
    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------
    
    /**
     * Validate before save
     * 
     * @param ValidationErrors $errors
     */
    function validate(ValidationErrors &$errors) {
      if($this->validatePresenceOf('name')) {
        if(!$this->validateUniquenessOf('name')) {
          $errors->addError(lang('Expense category name must be unique'), 'name');
        } // if
      } else {
        $errors->addError(lang('Expense category name is required'), 'name');
      } // if
    } // validate
    
  }