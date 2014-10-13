<?php

  /**
   * FeedClientSubscription class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class FeedClientSubscriptions extends ApiClientSubscriptions {

    /**
     * Get feed client subscription sorted by user
     *
     * @param $user FwUser
     * @return FeedClientSubscription
     */
    static function findByUser(FwUser $user) {
      return self::find(array(
        'conditions' => array("user_id = ? AND type = 'FeedClientSubscription' ", $user->getId()),
        'one' => true,
      ));
    } // findByUser

  } // FeedClientSubscription