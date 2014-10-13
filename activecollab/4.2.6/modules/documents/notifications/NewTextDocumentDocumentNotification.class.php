<?php

  /**
   * New text document notification
   *
   * @package activeCollab.modules.documents
   * @subpackage notifications
   */
  class NewTextDocumentDocumentNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Document ':name' has been Posted", array(
        'name' => $this->getParent() instanceof Document ? $this->getParent()->getName() : '',
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

      if($sender instanceof IUser && $parent instanceof Document) {
        return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> posted <a href=":parent_url" class="quick_view_item">":parent_name"</a> document', array(
          'author_url' => $this->getSender()->getViewUrl(),
          'author_name' => $this->getSender()->getDisplayName(true),
          'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
          'parent_name' => $parent->getName(),
          'parent_url' => $parent->getViewUrl(),
        ));
      } else {
        return parent::getMessageForWebInterface($user);
      } // if
    } // getMessageForWebInterface

  }