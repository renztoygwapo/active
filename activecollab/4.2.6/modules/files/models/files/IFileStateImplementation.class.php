<?php

/**
 * Project object state helper
 *
 * @package activeCollab.modules.system
 * @subpackage models
 */
class IFileStateImplementation extends IProjectObjectStateImplementation {

  /**
   * Delete implementation
   */
  function delete() {
    try {
      DB::beginWork('Deleting File');

      $files_to_delete = array($this->object->getLocation()); // add location to list of files which will be deleted

      $versions = $this->object->versions()->get(); // get all file versions

      // delete all file versions
      if (is_foreachable($versions)) {
        foreach ($versions as $version) {
          $files_to_delete[] = $version->getLocation();
          $version->delete(false);
        } // foreach
      } // if

      parent::delete(); // soft delete file itself

      // now delete all files
      if (is_foreachable($files_to_delete)) {
        foreach ($files_to_delete as $file_to_delete) {
          @unlink(UPLOAD_PATH . '/' . $file_to_delete); // remove file from system
        } // foreach
      } // if

      DB::commit('Successfully deleted file');
    } catch (Exception $e) {
      DB::rollback('Failed to delete file');
      throw $e;
    } // try
  } // delete

}