<?php

  /**
   * New notebook page notification
   *
   * @package activeCollab.modules.notebooks
   * @subpackage notifications
   */
  class NewNotebookPageNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("Page ':name' has been Created", array(
        'name' => $this->getParent() instanceof NotebookPage ? $this->getParent()->getName() : '',
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

      if($sender instanceof IUser && $parent instanceof NotebookPage) {
        if($this->isUserMentioned($user)) {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> mentioned you in the new <a href=":parent_url" class="quick_view_item">":parent_name"</a> notebook page', array(
            'author_url' => $this->getSender()->getViewUrl(),
            'author_name' => $this->getSender()->getDisplayName(true),
            'author_link_classes' => $this->getSender() instanceof User ? 'quick_view_item' : null,
            'parent_name' => $parent->getName(),
            'parent_url' => $parent->getViewUrl(true),
          ));
        } else {
          return lang('<a href=":author_url" class=":author_link_classes">:author_name</a> created <a href=":parent_url" class="quick_view_item">":parent_name"</a> notebook page', array(
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

    /**
     * Return notebook instance
     *
     * @return Notebook
     */
    function getNotebook() {
      return DataObjectPool::get('Notebook', $this->getAdditionalProperty('notebook_id'));
    } // getNotebook

    /**
     * Set parent notebook instace
     *
     * @param Notebook $notebook
     * @return NewNotebookPageNotification
     */
    function &setNotebook(Notebook $notebook) {
      $this->setAdditionalProperty('notebook_id', $notebook->getId());

      return $this;
    } // setNotebook

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'notebook' => $this->getNotebook(),
      );
    } // getAdditionalTemplateVars

  }