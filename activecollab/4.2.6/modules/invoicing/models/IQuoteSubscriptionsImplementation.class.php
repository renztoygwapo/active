<?php

  /**
   * Quote subscriptions implementation
   *
   * @package activeCollab.modules.invoicing
   * @subpackage models
   */
  class IQuoteSubscriptionsImplementation extends ISubscriptionsImplementation {
    
    /**
     * Construct quote subscriptions implementation
     *
     * @param ISubscriptions $object
     */
    function __construct(ISubscriptions $object) {
      if($object instanceof Quote) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Quote');
      } // if
    } // __construct

    /**
     * @param User $user
     * @return array|mixed
     */
    function getAvailableUsers(User $user) {
      return Users::find(array(
        "conditions" => array("state >= ?", STATE_VISIBLE)
      ));
    } // getAvailableUsers

    /**
     * Get subscribers
     *
     * @return array
     */
    function get() {
      $subscribers = parent::get();

      // if the quote is expired, do not notify anonymous subscribers
      // because they cannot see the quote any more
      if ($this->object->getStatus() >= QUOTE_STATUS_SENT && $this->object->isPublicPageExpired()) {
        if (is_foreachable($subscribers)) {
          foreach ($subscribers as $key => $subscriber) {
            if ($subscriber instanceof AnonymousUser) {
              unset($subscribers[$key]);
            } // if
          } // foreach
        } // if
      } // if

      return is_foreachable($subscribers) ? $subscribers : null;
    } // if
    
    /**
     * Returns true if $user can subscribe to this object
     *
     * @param User $user
     * @return boolean
     */
    function canSubscribe(User $user) {
      return $this->object->canView($user);
    } // canSubscribe

    /**
     * Returns true if $user can unsubscribe from this object
     *
     * @param User $user
     * @return boolean
     */
    function canUnsubscribe(User $user) {
      return $this->object->canView($user);
    }
    
  }