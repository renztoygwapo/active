<?php

  /**
   * Object reopened notification
   *
   * @package angie.frameworks.complete
   * @subpackage notifications
   */
  class FwObjectReopenedNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $parent = $this->getParent();

      return lang("':name' :type Reopened", array(
        'name' => $parent instanceof ApplicationObject ? $parent->getName() : '',
        'type' => $parent instanceof ApplicationObject ? $parent->getVerboseType(true, $user->getLanguage()) : '',
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return message for web interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $sender = $this->getSender();
      $parent = $this->getParent();

      if($sender instanceof IUser && $parent instanceof ApplicationObject) {
        return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> has reopened <a href=":parent_url" class="quick_view_item">":parent_name"</a> :parent_type', array(
          'author_url' => $sender->getViewUrl(),
          'author_name' => $sender->getDisplayName(true),
          'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
          'parent_name' => $parent->getName(),
          'parent_url' => $parent->getViewUrl(),
          'parent_type' => $parent->getVerboseType(true, $user->getLanguage()),
        ));
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

  }