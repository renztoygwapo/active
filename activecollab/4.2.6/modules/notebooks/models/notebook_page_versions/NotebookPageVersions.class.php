<?php

  /**
   * NotebookPageVersions class
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class NotebookPageVersions extends BaseNotebookPageVersions {
    
    /**
     * Return version instance based on notebook page and version number
     * 
     * @param NotebookPage $page
     * @param integer $version
     * @return NotebookPageVersion
     */
    static function findByPageAndVersion(NotebookPage $page, $version) {
      return NotebookPageVersions::find(array(
        'conditions' => array('notebook_page_id = ? AND version = ?', $page->getId(), $version),
        'order' => 'version DESC',
        'one'   => true
      ));
    } // findByPageAndVersion
  
    /**
     * Return notebook page versions
     *
     * @param NotebookPage $notebook_page
     * @param integer $version
     * @return array
     */
    static function findByNotebookPage($notebook_page, $version = null) {
      return NotebookPageVersions::find(array(
        'conditions' => array('notebook_page_id = ?', $notebook_page->getId()),
        'order' => 'version DESC',
      ));
    } // findByNotebookPage
    
    /**
     * Return number of versions for a given notebook page
     *
     * @param NotebookPage $notebook_page
     * @return integer
     */
    static function countByNotebookPage($notebook_page) {
      return NotebookPageVersions::count(array('notebook_page_id = ?', $notebook_page->getId()));
    } // countByNotebookPage
    
    /**
     * Find previous version
     *
     * @param ApplicationObject $for
     * @return NotebookPageVersion
     */
    static function findPrevious($for) {
      if($for instanceof NotebookPage) {
        return NotebookPageVersions::find(array(
          'conditions' => array('notebook_page_id = ?', $for->getId()),
          'order'      => 'version DESC',
          'offset'     => 0,
          'limit'      => 1,
          'one'        => true,
        ));
      } elseif($for instanceof NotebookPageVersion) {
        return NotebookPageVersions::find(array(
          'conditions' => array('notebook_page_id = ? AND version < ?', $for->getNotebookPageId(), $for->getVersion()),
          'order'      => 'version DESC',
          'offset'     => 0,
          'limit'      => 1,
          'one'        => true,
        ));
      } else {
        return null;
      } // if
    } // findPrevious
    
    /**
     * Find notebook page versions by list of ID-s
     *
     * @param array $ids
     * @return DBResult
     */
    static function findByNotebookPageIds($ids) {
      return NotebookPageVersions::find(array(
        'conditions' => array('notebook_page_id IN (?)', $ids),
        'order' => 'created_on DESC',
      ));
    } // findByIds
  
  }