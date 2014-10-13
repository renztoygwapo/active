<?php

  /**
   * File versions class
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class FileVersions extends BaseFileVersions {
    
    /**
     * Return file version by file ID and version num
     * 
     * @param integer $file_id
     * @param integer $version_num
     * @return FileVersion
     */
    static function findByFileIdAndVersionNum($file_id, $version_num) {
      return FileVersions::find(array(
        'conditions' => array('file_id = ? AND version_num = ?', $file_id, $version_num), 
        'one' => true, 
      ));
    } // findByFileIdAndVersionNum
  
    /**
     * Return file versions by file
     *
     * @param File $file
     * @return DBResult
     */
    static function findByFile(File $file) {
      return FileVersions::find(array(
        'conditions' => array('file_id = ?', $file->getId()), 
        'order' => 'version_num DESC', 
      ));
    } // findByFile
    
    /**
     * Return number of versions for a given file
     * 
     * @param File $file
     * @return integer
     */
    static function countByFile(File $file) {
      return DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'file_versions WHERE file_id = ?', $file->getId());
    } // countByFile
    
    /**
     * Return last revision by file
     * 
     * @param File $file
     * @return FileVersion
     */
    static function findLastByFile(File $file) {
      return FileVersions::find(array(
        'conditions' => array('file_id = ?', $file->getId()), 
        'order' => 'version_num DESC',
      	'one' => true
      ));
    } // findLastByFile
  
  }