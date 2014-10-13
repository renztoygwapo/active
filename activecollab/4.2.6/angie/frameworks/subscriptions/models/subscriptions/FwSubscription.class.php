<?php

  /**
   * Framework level subscription implementation
   *
   * @package angie.frameworks.subscriptions
   * @subpackage models
   */
  abstract class FwSubscription extends BaseSubscription {

    /**
     * Get subscribed user
     *
     * @return IUser
     */
    function getUser() {
      if ($this->getUserId()) {
        return DataObjectPool::get('User', $this->getUserId());
      } else {
        return new AnonymousUser($this->getUserName(), $this->getUserEmail());
      } // if
    } // getUser
  }