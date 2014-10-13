<?php

// Build on top of application controller
AngieApplication::useController('disk_space_admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

/**
 * Disk Space Attachments controller implementation
 *
 * @package angie.frameworks.attachments
 * @subpackage controller
 */
class FwPreviewDiskSpaceAdminController extends DiskSpaceAdminController {

  /**
   * Remove thumbnails
   */
  function remove_thumbnails() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    if (AngieApplication::isOnDemand()) {
      $this->response->badRequest();
    } // if

    try {
      Thumbnails::cacheClear();
      $this->response->respondWithData(DiskSpace::describe());
    } catch (Exception $e) {
      $this->response->exception($e);
    } // try
  } // remove_thumbnails

}