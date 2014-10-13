<?php

  /**
   * Framework level history implementation
   *
   * @package angie.frameworks.history
   * @subpackage models
   */
  class IHistoryImplementation {
    
    /**
     * Parent object
     *
     * @var ApplicationObject|IHistory
     */
    protected $object;
    
    /**
     * Array of fields that need to be tracked for the parent object
     *
     * @var array
     */
    protected $fields = array();
    
    /**
     * Name of the renderer class
     *
     * @var string
     */
    protected $renderer_class = 'HistoryRenderer';
    
    /**
     * Construct modification log helper
     *
     * @param IHistory $object
     * @param array $fields
     * @param string $render_class
     */
    function __construct(IHistory $object, $fields = null, $render_class = null) {
      $this->object = $object;
      
      if($this->object->fieldExists('parent_type') && $this->object->fieldExists('parent_id')) {
        $this->fields[] = 'parent_type';
        $this->fields[] = 'parent_id';
      } // if
      
      if($this->object->fieldExists('name')) {
        $this->fields[] = 'name';
      } // if
      
      if($this->object->fieldExists('body'))  {
        $this->fields[] = 'body';
      } // if
      
      if($this->object instanceof IState) {
        $this->fields[] = 'state';
      } // if
      
      if($this->object instanceof IVisibility) {
        $this->fields[] = 'visibility';
      } // if
      
      if($this->object instanceof IComplete) {
        $this->fields[] = 'priority';
        $this->fields[] = 'due_on';
        $this->fields[] = 'completed_on';
      } // if
      
      if($this->object instanceof ILabel) {
        $this->fields[] = 'label_id';
      } // if
      
      if($this->object instanceof ICategory) {
        $this->fields[] = 'category_id';
      } // if
      
      if($this->object instanceof IAssignees) {
        $this->fields[] = 'assignee_id';
      } // if
      
      if($this->object instanceof IComments) {
        $this->fields[] = 'is_locked';
      } // if
      
      if($fields) {
        foreach($fields as $field) {
          if(!in_array($field, $this->fields)) {
            $this->fields[] = $field;
          } // if
        } // foreach
      } // if
      
      if($render_class) {
        $this->renderer_class = $render_class;
      } // if
    } // __construct
    
    /**
     * Adds $fields to the list of tracked fields and returns $this
     * 
     * @param array $fields
     * @return IHistoryImplementation
     */
    function alsoTrackFields($fields) {
      if($fields) {
        if(is_array($fields)) {
          foreach($fields as $field) {
            if(!in_array($field, $this->fields)) {
              $this->fields[] = $field;
            } // if
          } // foreach
        } else {
          if(!in_array($fields, $this->fields)) {
            $this->fields[] = $fields;
          } // if
        } // if
      } // if
      
      return $this;
    } // alsoTrackFields
    
    /**
     * Remove fields from tracking list
     * 
     * @param array $fields
     * @return $this
     */
    function &alsoRemoveFields($fields) {
      if($fields) {
        if(is_array($fields)) {
          foreach($fields as $field) {
            array_remove_by_value($this->fields, $field);
          } // foreach
        } else {
          array_remove_by_value($this->fields, $fields);
        } // if
      } // if
      
      return $this;
    } // alsoRemoveFields
    
    /**
     * Return renderer class
     * 
     * @return HistoryRenderer
     * @throws InvalidParamError
     */
    function getRenderer() {
      $class = $this->renderer_class;
      
      if(class_exists($class, true)) {
        $renderer = new $class($this->object);
        
        if($renderer instanceof HistoryRenderer) {
          return $renderer;
        } else {
          throw new InvalidParamError('renderer_class', $this->renderer_class, 'Invalid renderer class');
        } // if
      } else {
        throw new InvalidParamError('renderer_class', $this->renderer_class, 'Renderer class not set');
      } // if
    } // getRenderer
    
    /**
     * Set renderer class
     * 
     * @param string $value
     * @return IHistoryImplementation
     */
    function &alsoSetRendererClass($value) {
      $this->renderer_class = $value;
      
      return $this;
    } // alsoSetRendererClass
    
    /**
     * Render history elements
     *
     * @param IUser $user
     * @param Smarty $smarty
     * @return array
     */
    function render(IUser $user, &$smarty) {
      return $this->getRenderer()->render($user, $smarty);
    } // render
    
    /**
     * Return tracked fields
     *
     * @param array $additional
     * @return array
     */
    function getTrackedFields($additional = null) {
      return $this->fields;
    } // getTrackedFields
    
    /**
     * Commit object modifications
     *
     * @param array $modified_fields
     * @param IUser $by
     * @param boolean $is_first
     */
    function commitModifications($modified_fields, IUser $by, $is_first = false) {
      $track_fields = $this->getTrackedFields();
      
      $to_log = array();
      foreach($modified_fields as $field => $value) {
        if($is_first && empty($value[1])) {
          continue; // Skip empty values for new objects
        } // if
        
        if(in_array($field, $track_fields)) {
          $to_log[] = $field;
        } // if
      } // foreach
      
      if(count($to_log)) {
        $log_table = TABLE_PREFIX . 'modification_logs';
        
        try {
          DB::beginWork('Commit object modification @ ' . __CLASS__);

          if($this->object->fieldExists('created_on') && $this->object->getFieldValue('created_on') != null && $is_first) {
            $created_on = $this->object->getCreatedOn(); // Use if object is just created and we set up created_on (used for BC importer)
          } //if

          if(empty($created_on)) {
            $created_on = new DateTimeValue();
          } // if

          $date_time = $created_on->toMySQL();

          DB::execute("INSERT INTO $log_table (parent_type, parent_id, created_on, created_by_id, created_by_name, created_by_email, is_first) VALUES (?, ?, ?, ?, ?, ?, ?)", get_class($this->object), $this->object->getId(), $date_time, $by->getId(), $by->getName(), $by->getEmail(), (boolean) $is_first);
          
          $log_id = DB::lastInsertId();
          
          $batch = new DBBatchInsert(TABLE_PREFIX . 'modification_log_values', array('modification_id', 'field', 'value'));

          foreach($to_log as $field) {
            if($modified_fields[$field] === null) {
              $batch->insert($log_id, $field, null);
            } elseif(is_array($modified_fields[$field])) {
              $batch->insert($log_id, $field, $modified_fields[$field][1]);
            } else {
              $batch->insert($log_id, $field, (string) $modified_fields[$field]);
            } // if
          } // foreach
          
          $batch->done();
          
          DB::commit('Object modifications commited @ ' . __CLASS__);
        } catch(Exception $e) {
          DB::rollback('Failed to commit object modifications @ ' . __CLASS__);
          
          throw $e;
        } // try
      } // if
    } // commitModifications
    
  }