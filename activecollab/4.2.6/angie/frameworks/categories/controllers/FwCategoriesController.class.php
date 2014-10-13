<?php

  /**
   * Framework level categories controller
   *
   * @package angie.frameworks.categories
   * @subpackage controllers
   */
  abstract class FwCategoriesController extends Controller {
    
    /**
     * Parent context
     *
     * @var ICategoriesContext
     */
    protected $categories_context;
    
    /**
     * Routing context name
     *
     * @var string
     */
    protected $routing_context;
    
    /**
     * Routing context parameters
     *
     * @var array
     */
    protected $routing_context_params;
    
    /**
     * Category class name
     *
     * @var string
     */
    protected $category_class;
    
    /**
     * Selected category
     *
     * @var Category
     */
    protected $active_category;
    
    /**
     * Active object
     * 
     * @var ICategory
     */
    protected $active_object;
    
    /**
     * Execute code before action has been executed
     */
    function __before() {
      parent::__before();
      
      $category_id = $this->request->get('category_id');
      if($category_id) {
        $this->active_category = Categories::findById($category_id);
        
        if(!($this->active_category instanceof $this->category_class)) {
          $this->response->operationFailed();
        } // if
      } // if
      
      if(!($this->active_category instanceof $this->category_class)) {
        $this->active_category = new $this->category_class();
      } // if
      
      $this->smarty->assign(array(
      	'category_context' => $this->categories_context,
      	'category_class' => $this->category_class,
      	'active_category' => $this->active_category,
      	'active_object' => $this->active_object
      ));
    } // __before
    
    /**
     * Manage categories
     */
    function categories() {
      if($this->request->isAsyncCall() || $this->request->isApiCall()) {
        $categories = $this->categories_context instanceof ICategoriesContext ? 
          $this->categories_context->availableCategories()->get($this->category_class) : 
          Categories::findBy(null, $this->category_class);
        
        if($this->request->isApiCall()) {
          $this->response->respondWithData($categories, array('as' => 'categories'));
        } else {
          $this->response->assign(array(
            'categories' => $categories, 
            'add_category_url' => Router::assemble($this->routing_context . '_categories_add', $this->routing_context_params), 
          ));
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // categories
    
    /**
     * Display content of a specific category
     */
    function view_category() {
      if($this->request->isApiCall()) {
        if($this->active_category->isLoaded()) {
          $this->response->respondWithData($this->active_category, array(
            'as' => 'category', 
          	'detailed' => true,  
          ));
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // view_category
    
    /**
     * Add category
     */
    function add_category() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        try {
          $this->active_category = new $this->category_class();
          
          $this->active_category->setAttributes($this->request->post('category'));
          $this->active_category->setParent($this->categories_context);
          
          $this->active_category->save();

          Categories::dropCache($this->categories_context, $this->active_category->getType());
          
          $this->response->respondWithData($this->active_category, array(
            'as' => 'category', 
            'detailed' => true, 
          ));
        } catch(Exception $e) {
          $this->response->exception($e);
        } // try
      } else {
        $this->response->badRequest();
      } // if
    } // add_category
    
    /**
     * Update selected category
     */
    function edit_category() {
      if(($this->request->isAsyncCall() || $this->request->isApiCall()) && $this->request->isSubmitted()) {
        if($this->active_category->isLoaded()) {
          if($this->active_category->canEdit($this->logged_user)) {
            try {
              $this->active_category->setAttributes($this->request->post('category'));
              $this->active_category->save();

              Categories::dropCache($this->categories_context, $this->active_category->getType());
              
              $this->response->respondWithData($this->active_category, array(
                'as' => 'category', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // edit_category
    
    /**
     * Delete selected category
     */
    function delete_category() {
      if(($this->request->isApiCall() || $this->request->isAsyncCall()) && $this->request->isSubmitted()) {
        if($this->active_category->isLoaded()) {
          if($this->active_category->canDelete($this->logged_user)) {
            try {
              $this->active_category->delete();

              Categories::dropCache($this->categories_context, $this->active_category->getType());

              $this->response->respondWithData($this->active_category, array(
                'as' => 'category', 
                'detailed' => true, 
              ));
            } catch(Exception $e) {
              $this->response->exception($e);
            } // try
          } else {
            $this->response->forbidden();
          } // if
        } else {
          $this->response->notFound();
        } // if
      } else {
        $this->response->badRequest();
      } // if
    } // delete_category
    
    /**
     * Update category for object with dialog
     */
    function update_category() {
    	if (!$this->request->isAsyncCall()) {
    		$this->response->badRequest();
    	} // if
    	
    	if (!($this->active_object instanceof ICategory) || !($this->active_object instanceof ApplicationObject)) {
    		$this->response->notFound();
    	} // if
    	
    	if ($this->active_object->isNew()) {
    		$this->response->notFound();
    	} // if
    	
    	if (!$this->active_object->canEdit($this->logged_user)) {
    		$this->response->forbidden();
    	} // if
    	
      $object_data = $this->request->post('object', array(
        'category_id' => $this->active_object->getCategoryId(),
      ));
      
      $this->smarty->assign('object_data', $object_data);
      
      if ($this->request->isSubmitted()) {
        try {
          DB::beginWork('Updating category');
          $this->active_object->setAttributes($object_data);
          $this->active_object->save();
          DB::commit('Category Updated');
          
          $this->response->respondWithData($this->active_object, array(
            'as' => $this->active_object->getBaseTypeName(),
            'detailed' => true
          ));
        } catch (Exception $e) {
          DB::rollback('Failed to save changes to category @ ' . __CLASS__);
          $this->response->exception($e);
        } // try
      } // if
    } // update_category
    
  }