<?php

// Build on top of application controller
AngieApplication::useController('disk_space_admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

/**
 * Disk Space Attachments controller implementation
 *
 * @package angie.frameworks.attachments
 * @subpackage controller
 */
class FwAttachmentsDiskSpaceAdminController extends DiskSpaceAdminController {

  /**
   * Remove temporary attachments controller
   */
  function remove_temporary_attachments() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      Attachments::cleanUp(0);
      $this->response->respondWithData(DiskSpace::describe());
    } catch (Exception $e) {
      $this->response->exception($e);
    } // try
  } // remove_temporary_attachments

}