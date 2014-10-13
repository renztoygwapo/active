<?php

  /**
   * Handle public unsubscribe event
   *
   * @package angie.frameworks.subscriptions
   * @subpackage helpers
   */

  /**
   * Handle on_handle_public_unsubscribe event
   *
   * @param string $notification
   * @param array $parts
   * @param null|boolean $unsubscribed
   * @param string $message
   * @param string $undo_url
   */
  function subscriptions_handle_on_handle_public_unsubscribe($notification, $parts, &$unsubscribed, &$message, &$undo_url) {
    if($notification == 'SUBS' && $unsubscribed === null) {
      list($subscription_id, $subscription_code) = $parts;

      if ($subscription_id && $subscription_code) {
        $subscription = Subscriptions::findById($subscription_id);

        if ($subscription instanceof Subscription) {
          if (strtoupper($subscription->getCode()) == $subscription_code) {
            $subscription->delete();
            if ($subscription->getUser() instanceof User) {
              AngieApplication::cache()->removeByObject($subscription->getUser(), 'subscriptions');
            } // if

            $unsubscribed = true;
            $message = lang('User :email_address has been successfully removed from ":object_name" notification list', array(
              'email_address' => $subscription->getUserEmail(),
              'object_name' => $subscription->getParent()->getName(),
            ));
          } else {
            $this->response->notFound();
          } // if
        } else {
          $unsubscribed = false;
        } // if
      } else {
        $unsubscribed = false;
      } // if
    } // if
  } // subscriptions_handle_on_handle_public_unsubscribe