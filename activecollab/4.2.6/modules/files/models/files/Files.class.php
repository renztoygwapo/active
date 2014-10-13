<?php

  /**
   * Files manager class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class Files extends ProjectAssets {

    /**
     * Force delete files by IDs
     *
     * @param array $ids
     * @throws Exception
     */
    static function forceDeleteByIds($ids) {
      try {
        DB::beginWork('Removing files by IDs @ ' . __CLASS__);

        if(is_foreachable($ids)) {
          $files = ProjectAssets::find(array(
            'conditions' => array('id IN (?) AND type = ?', $ids, 'File')
          ));

          if(is_foreachable($files)) {
            foreach($files as $file) {
              $files_to_delete = array($file->getLocation());

              $versions = $file->versions()->get();
              if(is_foreachable($versions)) {
                foreach($versions as $version) {
                  $files_to_delete[] = $version->getLocation();
                  $version->delete(false);
                } // foreach
              } // if

              if(is_foreachable($files_to_delete)) {
                foreach($files_to_delete as $file_to_delete) {
                  @unlink(UPLOAD_PATH . '/' . $file_to_delete);
                } // foreach
              } // if
            } // foreach
          } // if
        } // if

        DB::commit('Files removed by IDs @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to remove files by IDs @ ' . __CLASS__);
        throw $e;
      } // try
    } // forceDeleteByIds

  }