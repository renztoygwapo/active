<?php

  /**
   * New discussion notification
   *
   * @package activeCollab.modules.discussions
   * @subpackage notifications
   */
  class NewDiscussionNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Discussion ':object_name' has been started", array(
        'object_name' => $this->getParent() instanceof Discussion ? $this->getParent()->getName() : '',
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

      if($sender instanceof IUser && $parent instanceof Discussion) {
        if($this->isUserMentioned($user)) {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> mentioned you in the new <a href=":parent_url" class="quick_view_item">":parent_name"</a> discussion', array(
            'author_url' => $this->getSender()->getViewUrl(),
            'author_name' => $this->getSender()->getDisplayName(true),
            'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
            'parent_name' => $parent->getName(),
            'parent_url' => $parent->getViewUrl(true),
          ));
        } else {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> has started the <a href=":parent_url" class="quick_view_item">":parent_name"</a> discussion', array(
            'author_url' => $this->getSender()->getViewUrl(),
            'author_name' => $this->getSender()->getDisplayName(true),
            'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
            'parent_name' => $parent->getName(),
            'parent_url' => $parent->getViewUrl(true),
          ));
        } // if
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

  }