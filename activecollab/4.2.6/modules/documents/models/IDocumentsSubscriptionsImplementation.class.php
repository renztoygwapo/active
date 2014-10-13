<?php

  /**
   * Documents subscriptions implementation
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class IDocumentsSubscriptionsImplementation extends ISubscriptionsImplementation {

    /**
     * Construct document subscriptions implementation
     *
     * @param ISubscriptions $object
     * @throws InvalidInstanceError
     */
    function __construct(ISubscriptions $object) {
      if($object instanceof Document) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Document');
      } // if
    } // __construct

    /**
     * Return array of available users
     *
     * @param User $user
     * @return User[]
     */
    function getAvailableUsers($user) {
      $type_filter = $this->object->getVisibility() == VISIBILITY_PRIVATE ? Users::userClassesThatCanSeePrivate() : null;

      $user_ids = Users::findIdsByType($type_filter, STATE_VISIBLE, null, function($id, $type, $custom_permissions, $state) {
        if($type == 'Administrator') {
          return true;
        } elseif($type == 'Manager') {
          return in_array('can_manage_documents', $custom_permissions);
        } else {
          return in_array('can_use_documents', $custom_permissions);
        } // if
      });

      return $user_ids ? Users::findByIds($user_ids) : null;
    } // getAvailableUsers
  }
