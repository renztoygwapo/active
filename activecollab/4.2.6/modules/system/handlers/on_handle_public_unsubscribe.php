<?php

  /**
   * Handle public unsubscribe event
   *
   * @package activeCollab.modules.system
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
  function system_handle_on_handle_public_unsubscribe($notification, $parts, &$unsubscribed, &$message, &$undo_url) {
    if($notification == 'MRNGPPR' && $unsubscribed === null) {
      list($user_id, $subscription_code) = $parts;

      if ($user_id && $subscription_code) {
        $user = Users::findById($user_id);

        if($user instanceof User && $user->getState() >= STATE_VISIBLE) {
          if (strtoupper($user->getAdditionalProperty('subscription_code')) == $subscription_code) {
            ConfigOptions::setValueFor('morning_paper_enabled', $user, false);

            $unsubscribed = true;
            $message = lang(':user_name has been successfully removed from Morning Paper list', array(
              'user_name' => $user->getDisplayName(),
            ));

            $undo_url = Router::assemble('public_notifications_subscribe', array(
              'code' => MorningPaper::getSubscriptionCode($user),
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
  } // system_handle_on_handle_public_unsubscribe