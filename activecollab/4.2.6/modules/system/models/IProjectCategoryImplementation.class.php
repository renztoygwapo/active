<?php

  /**
   * Project category implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectCategoryImplementation extends ICategoryImplementation {
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory $object) {
      if($object instanceof Project) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Project');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return ProjectCategory
     */
    function newCategory() {
    	return new ProjectCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param Category $category
     * @return mixed
     */
    function set($category) {
      if($category) {
        if($category instanceof ProjectCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'ProjectCategory');
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
      return Router::assemble('project_categories_add');
    } // getAddCategoryUrl

  }