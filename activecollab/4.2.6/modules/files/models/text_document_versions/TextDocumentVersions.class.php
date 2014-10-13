<?php

  /**
   * TextDocumentVersions class
   *
   * @package activeCollab.modules.fies
   * @subpackage models
   */
  class TextDocumentVersions extends BaseTextDocumentVersions {
  	
  	/**
  	 * Find all document versions
  	 * 
  	 * @param TextDocument $document
  	 * @return DBResult
  	 */
		static function findByTextDocument(TextDocument $document) {
      return TextDocumentVersions::find(array(
        'conditions' => array('text_document_id = ?', $document->getId()), 
        'order' => 'created_on DESC', 
      ));
		} // findByTextDocument
		
		/**
		 * Find version by version num
		 * 
		 * @param TextDocument $document
		 * @param int $version
		 * @return TextDocumentVersion
		 */
    static function findByVersionNum(TextDocument $document, $version) {
			return TextDocumentVersions::find(array(
				'conditions' => array('text_document_id = ? AND version_num = ?', $document->getId(), $version), 
				'one' => true
			));
		} // findByVersion

    /**
     * Return number of versions for a given text document
     *
     * @param TextDocument $text_document
     * @return integer
     */
    static function countByTextDocument(TextDocument $text_document) {
      return DB::executeFirstCell('SELECT COUNT(*) FROM ' . TABLE_PREFIX . 'text_document_versions WHERE text_document_id = ?', $text_document->getId());
    } // countByFile
  
  }