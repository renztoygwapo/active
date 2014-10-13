<?php

  /**
   * Document search item implementation
   * 
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class IDocumentSearchItemImplementation extends ISearchItemImplementation {
    
    /**
     * Return list of indices that index parent object
     * 
     * Result is an array where key is the index name, while value is list of 
     * fields that's watched for changes
     * 
     * @return array
     */
    function getIndices() {
      return array(
        'documents' => array('category_id', 'name', 'body'), 
        'names' => array('name', 'body'),
      );
    } // getIndices
  
//    /**
//     * Return item context for given index
//     * 
//     * @param SearchIndex $index
//     * @return string
//     */
//    function getContext(SearchIndex $index) {
//      if($index instanceof DocumentsSearchIndex) {
//        return null;
//      } elseif($index instanceof NamesSearchIndex) {
//        return 'documents';
//      } else {
//        throw new InvalidInstanceError('index', $index, array('DocumentsSearchIndex', 'NamesSearchIndex'));
//      } // if
//    } // getContext
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      
      // Additional fields for primary documents index
      if($index instanceof DocumentsSearchIndex) {
        return array(
          'category_id' => $this->object->getCategoryId(), 
          'category' => $this->object->category()->get() instanceof DocumentCategory ? $this->object->category()->get()->getName() : null, 
          'name' => $this->object->getName(), 
          'body' => $this->object->getType() == Document::TEXT ? $this->object->getBody() : null, 
        );
        
      // Additional properties for names index
      } elseif($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(), 
        );
        
      // Invalid index type
      } else {
        throw new InvalidInstanceError('index', $index, array('DocumentsSearchIndex', 'NamesSearchIndex'));
      } // if
      
    } // getAdditional
    
  }