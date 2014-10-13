<?php

  /**
   * Discussion category implementation
   *
   * @package activeCollab.modules.discussions
   * @subpackage models
   */
  class IDiscussionCategoryImplementation extends IProjectObjectCategoryImplementation {

    /**
     * Name of the add category route
     *
     * @var string
     */
    protected $add_category_route = 'project_discussion_categories_add';
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory $object) {
      if($object instanceof Discussion) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Discussion');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return DiscussionCategory
     */
    function newCategory() {
    	return new DiscussionCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param DiscussionCategory $category
     * @return mixed
     */
    function set($category) {
      if($category) {
        if($category instanceof DiscussionCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'DiscussionCategory');
        }
      } else {
        $this->object->setCategoryId(null);
      } // if
    } // set
    	
	  /**
	   * Get category context
     *
     * @return ApplicationObject
	   */
	  function getCategoryContext() {
			return $this->object->getProject();    	
	  } // getCategoryContext

  }