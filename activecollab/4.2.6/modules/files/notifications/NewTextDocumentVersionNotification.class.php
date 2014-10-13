<?php

  /**
   * New text document version notification
   *
   * @package activeCollab.modules.files
   * @subpackage notifications
   */
  class NewTextDocumentVersionNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("New version of ':name' document has been posted", array(
        'name' => $this->getVersion() instanceof TextDocumentVersion ? $this->getVersion()->getName() : ''
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return full HTML message that can be used in application interface
     *
     * @param IUser $user
     * @return string
     */
    function getMessageForWebInterface(IUser $user) {
      $sender = $this->getSender();
      $parent = $this->getParent();

      if($sender instanceof IUser && $parent instanceof TextDocument) {
        return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> post a new version of <a href=":parent_url" class="quick_view_item">":parent_name"</a> document', array(
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

    /**
     * Return text document version instance
     *
     * @return TextDocumentVersion
     */
    function getVersion() {
      return DataObjectPool::get('TextDocumentVersion', $this->getAdditionalProperty('version_id'));
    } // getVersion

    /**
     * Set text document version instance
     *
     * @param TextDocumentVersion $version
     * @return NewTextDocumentVersionNotification
     */
    function &setVersion(TextDocumentVersion $version) {
      $this->setAdditionalProperty('version_id', $version->getId());

      return $this;
    } // setVersion

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'version' => $this->getVersion(),
      );
    } // getAdditionalTemplateVars

  }