<?php

  /**
   * Parent's category implementation
   *
   * @package angie.frameworks.categories
   * @subpackage models
   */
  abstract class ICategoryImplementation {
    
    /**
     * Parent object instance
     *
     * @var ICategory
     */
    protected $object;
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory &$object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Create new category
     * 
     * @return Category
     */
    abstract function newCategory();
    
    /**
     * category class
     * 
     * @var string
     */
    private $category_class = false;
    
    /**
     * Get category type
     *
     * @return string
     */
    function getCategoryClass() {
    	if ($this->category_class === false) {
    		$this->category_class = get_class($this->newCategory());
    	} // if
    	return $this->category_class;    	
    } // getCategoryClass
    
    /**
     * Return parent's category
     *
     * @return Category
     */
    function get() {
    	if($this->object->getCategoryId()) {
    		return DataObjectPool::get('Category', $this->object->getCategoryId());
    	} else {
    		return null;
    	} // if
    } // get
    
    /**
     * Set category
     *
     * @param Category $category
     * @return Category|null
     * @throws InvalidInstanceError
     */
    function set($category) {
      if($category) {
        if($category instanceof Category) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'Category');
        } // if
      } else {
        $this->object->setCategoryId(null);
      } // if

      return $category;
    } // set
    
    /**
     * Get category context
     *
     * @return ICategory
     */
    public function getCategoryContext() {
			return null;    	
    } // getCategoryContext

    /**
     * Return category context string
     *
     * @return string
     */
    public function getCategoryContextString() {
      $context = $this->getCategoryContext();
      if ($context &&  $context instanceof ApplicationObject) {
        if ($context->fieldExists('id')) {
          return get_class($context) . '_' . $context->getId();
        } else {
          return get_class($context);
        } // if
      } // if
      return null;
    } // getCategoryContextString

    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Return new category URL
     *
     * @return string
     */
    abstract function getAddCategoryUrl();

    /**
     * Update category url
     *
     * @return string
     */
    public function getUpdateCategoryUrl() {
      return $this->object->isLoaded() ? Router::assemble($this->object->getRoutingContext() . '_update_category', $this->object->getRoutingContextParams()) : '#';
    } // getUpdateLabelUrl
    
    /**
     * Describe category related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      if($detailed) {
        $result['category'] = $this->get() instanceof Category ? $this->get()->describe($user, false, $for_interface) : null;
        $result['urls']['update_category'] = $this->getUpdateCategoryUrl();
      } else {
        $result['category_id'] = $this->object->getCategoryId();
      } // if
    } // describe

    /**
     * Describe category related information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['category_id'] = $this->object->getCategoryId();

      if($detailed) {
        $result['category'] = $this->get() instanceof Category ? $this->get()->describeForApi($user) : null;
        $result['urls']['update_category'] = $this->getUpdateCategoryUrl();
      } // if
    } // describeForApi
    
  }