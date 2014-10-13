<?php

  /**
   * InvoiceNoteTemplate class
   * 
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class InvoiceNoteTemplate extends BaseInvoiceNoteTemplate implements IRoutingContext {
    
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
      
      $result['content'] = $this->getContent();
      $result['is_default'] = $this->getIsDefault();

      $result['urls']['set_as_default'] = $this->getSetAsDefaultUrl();
      $result['urls']['remove_default'] = $this->getRemoveDefaultUrl();
      
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
      throw new NotImplementedError(__METHOD__);
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
      return 'admin_invoicing_note';
    } // getRoutingContext
    
    /**
     * Return routing context parameters
     *
     * @return mixed
     */
    function getRoutingContextParams() {
      return array('note_id' => $this->getId());
    } // getRoutingContextParams
    
    // ---------------------------------------------------
    //  URLS
    // ---------------------------------------------------

    /**
     * Set as default url
     *
     * @return string
     */
    function getSetAsDefaultUrl() {
      return Router::assemble('admin_invoicing_note_set_as_default', array('note_id' => $this->getId()));
    } // getSetAsDefaultUrl

    /**
     * Remove default url
     *
     * @return string
     */
    function getRemoveDefaultUrl() {
      return Router::assemble('admin_invoicing_note_remove_default', array('note_id' => $this->getId()));
    } // getRemoveDefaultUrl

    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Returns true if $user can create a new instance of this type
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return $user->isAdministrator();
    } // canAdd

    /**
     * Returns true if $user can view this object
     *
     * @param User $user
     * @return boolean
     */
    function canView(User $user) {
      return $this->canAdd($user);
    } // canView

    /**
     * Returns true if $user can update this object
     *
     * @param User $user
     * @return boolean
     */
    function canEdit(User $user) {
      return $this->canAdd($user);
    } // canEdit

    /**
     * Returns true if $user can delete or move to trash this object
     *
     * @param User $user
     * @return boolean
     */
    function canDelete(User $user) {
      return $this->canAdd($user);
    } // canDelete

    // ---------------------------------------------------
    //  System
    // ---------------------------------------------------

    /**
     * Validate model
     *
     * @param ValidationErrors $errors
     */
    function validate(&$errors) {
      if (!$this->validatePresenceOf('name')) {
        $errors->addError(lang('Note name is required'), 'name');
      } // if
      
      if (!$this->validatePresenceOf('content')) {
        $errors->addError(lang('Note content is required'), 'unit_cost');
      } // if
      
      return parent::validate($errors);
    } // validate
  
  }