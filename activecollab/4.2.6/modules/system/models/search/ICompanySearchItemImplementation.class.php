<?php

  /**
   * Company search item implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ICompanySearchItemImplementation extends ISearchItemImplementation {
  
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
        'names' => array('name'), 
      );
    } // getIndices
  
//    /**
//     * Return item context for given index
//     * 
//     * @param SearchIndex $index
//     * @return string
//     */
//    function getContext(SearchIndex $index) {
//      if($index instanceof NamesSearchIndex) {
//        return 'people/' . $this->object->getId();
//      } else {
//        throw new InvalidInstanceError('index', $index, 'NamesSearchIndex');
//      } // if
//    } // getContext
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     */
    function getAdditional(SearchIndex $index) {
      if($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(), 
        	'visibility' => VISIBILITY_NORMAL, 
        );
      } else {
        throw new InvalidInstanceError('index', $index, 'NamesSearchIndex');
      } // if
    } // getAdditional
    
  }