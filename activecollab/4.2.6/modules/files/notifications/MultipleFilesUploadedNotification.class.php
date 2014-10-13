<?php
  /**
   * Notification that is sent when multiple files are uploaded
   *
   * @package activeCollab.modules.files
   * @subpackage notifications
   */
  class MultipleFilesUploadedNotification extends Notification {

    /**
     * Return notification message
     *
     * @param IUser $user
     * @return string
     */
    function getMessage(IUser $user) {
      $files_count = (integer) $this->getAdditionalProperty('files_count');

      if($files_count == 1) {
        return lang('One File has been Uploaded', null, true, $user->getLanguage());
      } else {
        return lang(':num Files have been Uploaded', array(
          'num' => $files_count,
        ), true, $user->getLanguage());
      } // if

    } // getMessage

    /**
     * Return files
     *
     * @return File[]
     */
    function getFiles() {
      return Files::findByIds($this->getAdditionalProperty('file_ids'), STATE_VISIBLE, VISIBILITY_PRIVATE);
    } // getFiles

    /**
     * Set list of files
     *
     * @param File[] $files
     * @return MultipleFilesUploadedNotification
     */
    function &setFiles($files) {
      $file_ids = array();

      foreach($files as $file) {
        $file_ids[] = $file->getId();
      } // if

      $this->setAdditionalProperty('files_count', count($file_ids));
      $this->setAdditionalProperty('file_ids', $file_ids);

      return $this;
    } // setFiles

    /**
     * Return project in which files were uploaded
     *
     * @return Project
     */
    function getProject() {
      return DataObjectPool::get('Project', $this->getAdditionalProperty('project_id'));
    } // getProject

    /**
     * Set project in which files were uploaded
     *
     * @param Project $project
     * @return MultipleFilesUploadedNotification
     */
    function &setProject(Project $project) {
      $this->setAdditionalProperty('project_id', $project->getId());

      return $this;
    } // setProject

    /**
     * Return additional template variables
     *
     * @param NotificationChannel $channel
     * @return array
     */
    function getAdditionalTemplateVars(NotificationChannel $channel) {
      return array(
        'files' => $this->getFiles(),
        'project' => $this->getProject(),
      );
    } // getAdditionalTemplateVars

  }