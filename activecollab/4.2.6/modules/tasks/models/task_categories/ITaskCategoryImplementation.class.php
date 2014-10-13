<?php

  /**
   * Task category implementation
   *
   * @package activeCollab.modules.tasks
   * @subpackage models
   */
  class ITaskCategoryImplementation extends IProjectObjectCategoryImplementation {

    /**
     * Name of the add category route
     *
     * @var string
     */
    protected $add_category_route = 'project_task_categories_add';
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     * @throws InvalidInstanceError
     */
    function __construct(ICategory $object) {
      if($object instanceof Task) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Task');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return TaskCategory
     */
    function newCategory() {
    	return new TaskCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param Category $category
     * @return Category|null|void
     * @throws InvalidInstanceError
     */
    function set($category) {
      if($category) {
        if($category instanceof TaskCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'TaskCategory');
        }
      } else {
        $this->object->setCategoryId(null);
      } // if
    } // set
    
    /**
     * Get category context
     */
    function getCategoryContext() {
			return $this->object->getProject();    	
    } // getCategoryContext

  }