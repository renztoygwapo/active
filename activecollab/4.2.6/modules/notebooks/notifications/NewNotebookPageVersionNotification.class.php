<?php

  /**
   * New notebook page notification
   *
   * @package activeCollab.modules.notebooks
   * @subpackage notifications
   */
  class NewNotebookPageVersionNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("New Version of ':name' Page has been Created", array(
        'name' => $this->getParent() instanceof NotebookPage ? $this->getParent()->getName() : '',
      ), true, $user->getLanguage());
    } // getMessage

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
     * @return NewNotebookPageVersionNotification
     */
    function &setNotebook(Notebook $notebook) {
      $this->setAdditionalProperty('notebook_id', $notebook->getId());

      return $this;
    } // setNotebook

    /**
     * Get page version
     *
     * @return NotebookPageVersion
     */
    function getVersion() {
      return DataObjectPool::get('NotebookPageVersion', $this->getAdditionalProperty('notebook_page_version_id'));
    } // getVersion

    /**
     * Set page version
     *
     * @param NotebookPageVersion $version
     * @return NewNotebookPageVersionNotification
     */
    function &setVersion(NotebookPageVersion $version) {
      $this->setAdditionalProperty('notebook_page_version_id', $version->getId());

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
        'notebook' => $this->getNotebook(),
        'notebook_page_version' => $this->getVersion(),
      );
    } // getAdditionalTemplateVars

  }