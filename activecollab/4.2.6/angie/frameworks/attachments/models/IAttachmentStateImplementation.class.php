<?php

/**
 * Attachment state helper
 *
 * @package angie.frameworks.attachments
 * @subpackage models
 */
class IAttachmentStateImplementation extends IStateImplementation {

  /**
   * Delete implementation
   */
  function delete() {
    try {
      DB::beginWork('Deleting Attachment');

      $file_to_delete = array($this->object->getLocation()); // remember location of the attachment

      parent::delete(); // soft delete attachment

      @unlink(UPLOAD_PATH . '/' . $file_to_delete); // remove file from system

      DB::commit('Successfully deleted Attachment');
    } catch (Exception $e) {
      DB::rollback('Failed to delete Attachment');
      throw $e;
    } // try
  } // delete

}