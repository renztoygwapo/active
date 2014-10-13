<?php

  AngieApplication::useController('shared_object', SYSTEM_MODULE);

  /**
   * Files Shared object controller delegate
   *
   * @package activeCollab.modules.files
   * @subpackage controllers
   */
  class FilesSharedObjectController extends SharedObjectController {

    /**
     * File download page action
     */
    function download() {
      if ($this->active_shared_object->sharing()->isExpired()) {
        $this->response->notFound();
      } // if

      if ($this->logged_user instanceof User) {
        $this->active_shared_object->accessLog()->log($this->logged_user);
        $this->active_shared_object->accessLog()->logDownload($this->logged_user);
      } else {
        $this->active_shared_object->accessLog()->logAnonymous();
        $this->active_shared_object->accessLog()->logAnonymousDownload();
      } // if

      // download the file
      $this->active_shared_object->download()->send(true);
    } // download

  }