<?php

  /**
   * Asset category implementation
   *
   * @package activeCollab.modules.files
   * @subpackage models
   */
  class IAssetCategoryImplementation extends IProjectObjectCategoryImplementation {

    /**
     * Name of the add category route
     *
     * @var string
     */
    protected $add_category_route = 'project_asset_categories_add';
    
    /**
     * Construct object's category helper
     *
     * @param ICategory $object
     */
    function __construct(ICategory $object) {
      if($object instanceof ProjectAsset) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'ProjectAsset');
      } // if
    } // __construct
    
    /**
     * Create new category
     * 
     * @return AssetCategory
     */
    function newCategory() {
    	return new AssetCategory();
    } // newCategory
    
    /**
     * Set category
     *
     * @param AssetCategory $category
     * @return mixed
     */
    function set($category) {
      if($category) {
        if($category instanceof AssetCategory) {
          $this->object->setCategoryId($category->getId());
        } else {
          throw new InvalidInstanceError('category', $category, 'AssetCategory');
        }
      } else {
        $this->object->setCategoryId(null);
      } // if
    } // set
    
    /**
     * Update category url
     * 
     * @return string
     */
    public function getUpdateCategoryUrl() {
			return Router::assemble('project_asset_update_category', $this->object->getRoutingContextParams());
    } // getUpdateLabelUrl
    
    /**
     * Get category context
     */
    function getCategoryContext() {
			return $this->object->getProject();    	
    } // getCategoryContext

  }