<?php

  /**
   * New file version notification
   *
   * @package activeCollab.modules.files
   * @subpackage notifications
   */
  class NewFileVersionNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      return lang("New Version of ':object_name' File has been Uploaded", array(
        'object_name' => $this->getParent() instanceof File ? $this->getParent()->getName() : ''
      ), true, $user->getLanguage());
    } // getMessage

    /**
     * Return file version instance
     *
     * @return FileVersion
     */
    function getVersion() {
      return DataObjectPool::get('FileVersion', $this->getAdditionalProperty('version_id'));
    } // getVersion

    /**
     * Set file version instance
     *
     * @param FileVersion $version
     * @return NewFileVersionNotification
     */
    function &setVersion(FileVersion $version) {
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