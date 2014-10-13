<?php

  // Build on top of system module
  AngieApplication::useController('frontend', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level public notifications controller
   *
   * @package angie.frameworks.subscriptions
   * @subpackage controllers
   */
  abstract class FwPublicNotificationsController extends FrontendController {

    /**
     * Subscribe user with one click
     */
    function subscribe() {
      $code = $this->request->get('code');

      if($code) {
        $parts = explode('-', strtoupper($code));

        if(count($parts) > 1) {
          $notification = array_shift($parts);

          $subscribed = null;
          $subscribed_message = $undo_url = '';

          EventsManager::trigger('on_handle_public_subscribe', array($notification, $parts, &$subscribed, &$subscribed_message, &$undo_url));

          if($subscribed !== null && $subscribed_message) {
            $this->response->assign(array(
              'subscribed' => $subscribed,
              'subscribed_message' => $subscribed_message,
              'undo_url' => $undo_url,
            ));
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // subscribe
    
    /**
     * One click unsubscribe, no login required
     */
    function unsubscribe() {
      $code = $this->request->get('code');

      if($code) {
        $parts = explode('-', strtoupper($code));

        if(count($parts) > 1) {
          $notification = array_shift($parts);

          $unsubscribed = null;
          $unsubscribed_message = $undo_url = '';

          EventsManager::trigger('on_handle_public_unsubscribe', array($notification, $parts, &$unsubscribed, &$unsubscribed_message, &$undo_url));

          if($unsubscribed !== null && $unsubscribed_message) {
            $this->response->assign(array(
              'unsubscribed' => $unsubscribed,
              'unsubscribed_message' => $unsubscribed_message,
              'undo_url' => $undo_url,
            ));
          } else {
            $this->response->notFound();
          } // if
        } else {
          $this->response->badRequest();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // unsubscribe

  }