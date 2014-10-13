<?php

  /**
   * Document category implementation
   *
   * @package activeCollab.modules.documents
   * @subpackage models
   */
  class IDocumentCategoryImplementation extends ICategoryImplementation {
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory $object) {
      if($object instanceof Document) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Document');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return DocumentCategory
     */
    function newCategory() {
    	return new DocumentCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param Category $category
     * @return mixed
     */
    function set($category) {
      if($category) {
        if($category instanceof DocumentCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'DocumentCategory');
        }
      } else {
        $this->object->setCategoryId(null);
      } // if
    } // set

    /**
     * Return new category URL
     *
     * @return string
     */
    function getAddCategoryUrl() {
      return Router::assemble('document_categories_add');
    } // getAddCategoryUrl

  }