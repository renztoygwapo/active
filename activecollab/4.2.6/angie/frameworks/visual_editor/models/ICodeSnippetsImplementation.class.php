<?php

  /**
   * Object code snippets implementation
   *
   * @package angie.framework.visual_editor
   * @subpackage models
   */
  class ICodeSnippetsImplementation {
    
    /**
     * Parent instance
     *
     * @var ICodeSnippets
     */
    protected $object;
    
    /**
     * Construct code snippets implementaiton and set parent object
     *
     * @param ICodeSnippets $object
     */
    function __construct(ICodeSnippets $object) {
      $this->object = $object;
    } // __construct
        
    /**
     * List of code snippets that are pending to have their parent type / ID set
     *
     * @var array
     */
    private $pending_parent = array();
    
    /**
     * List of code snippets ID-s that are pending deletion
     *
     * @var array
     */
    private $pending_deletion = array();
    
    // ---------------------------------------------------
    //  Pending code snippets handling
    // ---------------------------------------------------
    
    /**
     * Set list of code snippets ID-s that will be attached to parent object on
     * commit
     *
     * @param array $ids
     */
    function addPendingParent($ids) {
    	$this->pending_parent = array_merge($this->pending_parent, (array) $ids);
    } // addPendingParent
    
    /**
     * Add code snippets ID-s that will be deleted on commit
     *
     * @param array $ids
     */
    function addPendingDeletion($ids) {
    	$this->pending_deletion = array_merge($this->pending_deletion, (array) $ids);
    } // addPendingDeletion
    
    /**
     * Attach pending files to the object
     */
    function commitPending() {
      try {
        DB::beginWork('Commiting pending code snippets @ ' . __CLASS__);
        
        // Pending parent
        if (!empty($this->pending_parent)) {
        	DB::execute('UPDATE ' . TABLE_PREFIX . 'code_snippets SET parent_type = ?, parent_id = ? WHERE id IN (?)', get_class($this->object), $this->object->getId(), $this->pending_parent);
        } // if
        
        // Pending deletion
        if (!empty($this->pending_deletion)) {
          DB::execute('DELETE FROM ' . TABLE_PREFIX . 'code_snippets WHERE id IN (?)', $this->pending_deletion);
        } // if
        
        DB::commit('Commited pending code snippets @ ' . __CLASS__);
      } catch(Exception $e) {
        DB::rollback('Failed to commit pending code snippets @ ' . __CLASS__);
        throw $e;
      } // try
    } // commitPending
  }