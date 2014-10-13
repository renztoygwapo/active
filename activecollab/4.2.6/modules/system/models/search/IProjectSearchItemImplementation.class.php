<?php

  /**
   * Project search item implementation
   * 
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectSearchItemImplementation extends ISearchItemImplementation {
  
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
        'projects' => array('category_id', 'company_id', 'name', 'slug', 'overview', 'completed_on'), 
        'names' => array('name', 'body'),
      );
    } // getIndices
    
    /**
     * Return additional properties for a given index
     * 
     * @param SearchIndex $index
     * @return mixed
     * @throws InvalidInstanceError
     */
    function getAdditional(SearchIndex $index) {
      
      // Additional fields for primary projects index
      if($index instanceof ProjectsSearchIndex) {
        return array(
          'category_id' => $this->object->getCategoryId(), 
          'category' => $this->object->category()->get() instanceof ProjectCategory ? $this->object->category()->get()->getName() : null,
          'company_id' => $this->object->getCompanyId(), 
          'company' => $this->object->getCompany() instanceof Company ? $this->object->getCompany()->getName() : Companies::findOwnerCompany()->getName(), 
          'name' => $this->object->getName(), 
          'slug' => $this->object->getSlug(), 
          'overview' => $this->object->getOverview(), 
        );
        
      // Additional properties for names index
      } elseif($index instanceof NamesSearchIndex) {
        return array(
          'name' => $this->object->getName(),
          'body' => $this->object->getOverview(),
          'visibility' => VISIBILITY_NORMAL, 
        );
        
      // Invalid index type
      } else {
        throw new InvalidInstanceError('index', $index, array('ProjectsSearchIndex', 'NamesSearchIndex'));
      } // if
    } // getAdditional
    
  }