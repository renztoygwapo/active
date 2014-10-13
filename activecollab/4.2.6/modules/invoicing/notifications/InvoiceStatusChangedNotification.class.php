<?php

  /**
   * Invoice client notification
   *
   * @package activeCollab.modules.invoicing
   * @subpackage notifications
   */
  abstract class InvoiceStatusChangedNotification extends Notification {

    /**
     * Return visit URL
     *
     * @param IUser $user
     * @return string
     */
    function getVisitUrl(IUser $user) {
      if($user instanceof Client) {
        return $this->getParent() instanceof IRoutingContext ? $this->getParent()->getCompanyViewUrl() : '#';
      } //if
      return $this->getParent() instanceof IRoutingContext ? $this->getParent()->getViewUrl() : '#';
    } // getVisitUrl

    /**
     * Return full HTML message that can be used in application interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $message = $this->getMessage($user);

      if($this->getParent() instanceof ApplicationObject) {
        if($user instanceof Client) {
          return '<a href="' . clean($this->getParent()->getCompanyViewUrl()) . '" class="quick_view_item">' . $message . '</a>';
        } else {
          return '<a href="' . clean($this->getParent()->getViewUrl()) . '" class="quick_view_item">' . $message . '</a>';
        }
      } else {
        return $message;
      } // if
    } // getMessageForWebInterface


  }