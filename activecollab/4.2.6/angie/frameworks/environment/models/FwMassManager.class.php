<?php

  /**
   * Framework level mass manager
   * 
   * @package angie.frameworks.environment
   * @subpackage model
   */
  class FwMassManager implements IDescribe {
  	
  	/**
  	 * Consts
  	 */
  	const MULTIPLE_ACTION = 'multiple';
  	const SEPARATE_ACTION = 'separate';
  	
  	/**
  	 * Objects on which actions are performed
  	 * 
  	 * @var array
  	 */
  	protected $objects;
  	
  	/**
  	 * User who's performing mass update tasks
  	 * 
  	 * @var User
  	 */
  	protected $user;
  	
  	/**
  	 * Object which is used as a source for retrieving actions
  	 * 
  	 * @var ApplicationObject|IState
  	 */
  	protected $object;
  	
  	/**
  	 * Get actions for this object
  	 * 
  	 * @var array
  	 */
  	protected $actions = false;
  	  	
  	/**
  	 * Construct the mass managers
  	 * 
  	 * @param User $user
     * @param ApplicationObject $object
  	 */
  	function __construct($user, $object) {
  		$this->user = $user;
  		$this->object = $object;
  	} // __construct

  	/**
  	 * Adds a separate action
  	 * 
  	 * @param integer $id
  	 * @param array $data
  	 */
  	function addSeparateAction($id, $data) {
  		if ($this->actions === false) {
  			$this->actions = array();
  		} // if
  		
  		if (!isset($this->actions[self::SEPARATE_ACTION])) {
  			$this->actions[self::SEPARATE_ACTION] = array();
  		} // if
  		
  		$this->actions[self::SEPARATE_ACTION][$id] = $data;
  	} // addSeparateAction
  	
  	/**
  	 * Adds a multiple action
  	 * 
  	 * @param integer $id
  	 * @param array $data
  	 */
  	function addMultipleAction($id, $data) {
  		if ($this->actions === false) {
  			$this->actions = array();
  		} // if
  		
  		if (!isset($this->actions[self::MULTIPLE_ACTION])) {
  			$this->actions[self::MULTIPLE_ACTION] = array();
  		} // if
  		
  		$this->actions[self::MULTIPLE_ACTION][$id] = $data;
  	} // addMultipleAction

  	/**
  	 * Return array of actions that can be performed on $this->object
  	 * 
  	 * @return array
  	 */
  	function rebuildActions() {  		
  		$this->actions = false;
  		
  		// visibility related actions
  		if ($this->object->fieldExists('visibility')) {
  			$this->addMultipleAction('change_visibility', array(
          'title'    => lang('Change Visibility'),
          'controls' => '<select name="visibility"><option value="0">' . lang('Private') . '</option><option value="1">' . lang('Normal') . '</option></select>'
        )); 			
  		} // if
  		
  		// state related actions
  		if ($this->object instanceof IState) {
  			$this->addSeparateAction('move_to_trash', array(
	      	'title'					=> lang('Move to Trash'),
  				'icon'					=> AngieApplication::getImageUrl('icons/32x32/trash.png', ENVIRONMENT_FRAMEWORK),
  				'confirm'				=> lang('Are you sure that you want to move selected items to trash?'),
  				'after'					=> 'delete'  			 
     		));

        if ($this->object->getState() == STATE_ARCHIVED) {
          $this->addSeparateAction('restore_from_archive', array(
            'title'					=> lang('Restore From Archive'),
            'icon'					=> AngieApplication::getImageUrl('icons/32x32/unarchive.png', ENVIRONMENT_FRAMEWORK),
          ));
        } else {
          $this->addSeparateAction('move_to_archive', array(
            'title'					=> lang('Move to Archive'),
            'confirm'				=> lang('Are you sure that you want to move selected items to archive?'),
            'icon'					=> AngieApplication::getImageUrl('icons/32x32/archive.png', ENVIRONMENT_FRAMEWORK),
          ));
        } // if

  		} // if
  		
  		// complete framework actions
  		if (AngieApplication::isFrameworkLoaded('complete') && $this->object instanceof IComplete) {
  			$this->addMultipleAction('change_completed', array(
	        'title'    => lang('Change Status'),
	        'controls' => '<select name="complete"><option value="0">' . lang('Active') . '</option><option value="1">' . lang('Completed') . '</option></select>',
	      ));
	        
	      if ($this->object->fieldExists('priority')) {
	      	AngieApplication::useHelper('select_priority', COMPLETE_FRAMEWORK);
	  			$this->addMultipleAction('change_priority', array(
	  	     	'title' => lang('Change Priority'),
		       	'controls' => smarty_function_select_priority(array(
		       		'name'	=> 'priority',
		       		'value' => PRIORITY_NORMAL
	  				), SmartyForAngie::getInstance())
		      ));
	      } // if
  		} // if
  		
  		// category framework stuff
  		if (AngieApplication::isFrameworkLoaded('categories') && $this->object instanceof ICategory) {
        AngieApplication::useHelper('select_category', CATEGORIES_FRAMEWORK);
        
        $this->addMultipleAction('change_category', array(
          'title' => lang('Change Category'),
          'controls' => smarty_function_select_category(array(
            'name' => 'category_id',
            'type' => $this->object->category()->getCategoryClass(),
						'parent' => $this->object->category()->getCategoryContext(),
            'user' => $this->user,
            'id' => 'mass_edit_task_category',
            'on_new_category' => 'on_new_task_category',
            'add_url' => $this->object->category()->getAddCategoryUrl(),
            'success_event' => 'category_created'
          ), SmartyForAngie::getInstance())
        ));
  		} // if
  		
  		// label related framework stuff
  		if (AngieApplication::isFrameworkLoaded('labels') && $this->object instanceof ILabel) {  			
  			AngieApplication::useHelper('select_label', LABELS_FRAMEWORK);
  			$this->addMultipleAction('change_label', array(
          'title'    => lang('Change Label'),
          'controls' => smarty_function_select_label(array(
            'type' => $this->object->label()->getLabelType(),
            'user' => $this->user,
            'name' => 'label_id',
            'optional' => 'yes',
            'can_create_new'	=> 'no', 
          ), SmartyForAngie::getInstance())
        ));
  		} // if
  	} // rebuildActions
  	
  	/**
  	 * Perform mass update on $objects using $actions returning number of objects updated
  	 * 
  	 * @param array $objects
  	 * @param array $actions
  	 * @param array $variables
  	 * @return integer
     * @throws Error
  	 */
    function performUpdate($objects, $actions, $variables) {
  		$this->objects = $objects;
  		
  		if (!is_foreachable($actions)) {
  			return null;
  		} // if
  		
  		if (!is_foreachable($this->objects)) {
  			return null;
  		} // if
  		
  		$response = array();
  		
  		foreach ($this->objects as $object) {
  			$checksum = $object->getFieldsChecksum();
  			
  			foreach ($actions as $action) {
  				$method_name = 'action' . ucfirst(Inflector::camelize($action));
  				
  				if (method_exists($this, $method_name)) { 					
						$this->$method_name($object, $variables);
  				} else {
  					throw new Error('Action \'' . $action . '\' (' . $method_name . ') is not supported');
  				} // if
  			} // foreach

  			// if object require saving
  			if ($object->isModified()) {
  				$object->save();
  			} // if
  			  			
  			// if object has been updated
  			if ($checksum != $object->getFieldsChecksum()) {
  				$response[] = $object;
  			} // if
  		} // foreach
  		
  		return $response;
  	} // permformUpdate
  	  	
  	/**
  	 * Change visibility for object
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionChangeVisibility(&$object, $variables) {
  		if (!$this->object->fieldExists('visibility')) {
  			return false;
  		} // if
  		
			if (method_exists($object, 'canEdit') && !$object->canEdit($this->user)) {
				return false;
			} // if

  		$object->setVisibility((integer) array_var($variables, 'visibility'));
  	} // actionChangeVisibility
  	
  	/**
  	 * Change object's completed state
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionChangeCompleted(&$object, $variables) {
  		if (!($object instanceof IComplete)) {
  			return false;
  		} // if
  		
  		if (!$object->complete()->canChangeStatus($this->user)) {
  			return false;
  		} // if
  		
  		$complete = (boolean) array_var($variables, 'complete');
			if ($complete) {
				if ($object->complete()->isOpen()) {
					$object->complete()->complete($this->user);
				} // if
			} else {
				if ($object->complete()->isCompleted()) {
        	$object->complete()->open($this->user);
        } // if				
			} // if
  	} // actionChangeCompleted
  	
  	/**
  	 * Change priority
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionChangePriority(&$object, $variables) {
  		if (!($object instanceof IComplete)) {
  			return false;
  		} // if
  		
			if (method_exists($object, 'canEdit') && !$object->canEdit($this->user)) {
				return false;
			} // if
			  		
  		$priority = (integer) array_var($variables, 'priority');
  		$object->setPriority($priority);
  	} // actionChangePriority
  	
  	/**
  	 * Change category
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionChangeCategory(&$object, $variables) {
  		if (!($object instanceof ICategory)) {
  			return false;
  		} // if
  		
			if (method_exists($object, 'canEdit') && !$object->canEdit($this->user)) {
				return false;
			} // if
  		
  		$category_id = (integer) array_var($variables, 'category_id');

      if($category_id) {
        $category_type = $object->category()->getCategoryClass();
        $category = new $category_type($category_id);

        // wrong category type
        if ($category_type != get_class($category)) {
          $category = null;
        } // if
      } else {
        $category = null;
      } // if
  		

  		if($category instanceof Category) {
        $object->category()->set($category);
      } else {
        $object->category()->set(null);
      } // if
  	} // actionChangeCategory
  	
  	/**
  	 * Change label
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return boolean
  	 */
  	function actionChangeLabel(&$object, $variables) {
  		if (!($object instanceof ILabel)) {
  			return false;
  		} // if
  		
			if (method_exists($object, 'canEdit') && !$object->canEdit($this->user)) {
				return false;
			} // if
  		
  		$label_id = (integer) array_var($variables, 'label_id');
  		$label = $label_id ? DataObjectPool::get('Label', $label_id) : null;

      if($label instanceof Label) {
        $object->label()->set($label);
      } else {
        $object->label()->set(null);
      } // if
  	} // actionChangeLabel
  	
  	/**
  	 * Move object to trash
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionMoveToTrash(&$object, $variables) {
  		if (!($object instanceof IState)) {
  			return false;
  		} // if
  		
  		if (!$object->state()->canTrash($this->user)) {
  			return false;
  		} // if
  		
  		if ($object->getState() <= STATE_TRASHED) {
  			return false;
  		} // if
  		
  		$object->state()->trash();
  	} // actionMoveToTrash

  	/**
  	 * Move object to archive
  	 * 
  	 * @param ApplicationObject $object
  	 * @param array $variables
     * @return bool
  	 */
  	function actionMoveToArchive(&$object, $variables) {
  		if (!($object instanceof IState)) {
  			return false;
  		} // if
  		
  		if (!$object->state()->canArchive($this->user)) {
  			return false;
  		} // if

  		// can't move to archive if object is open
  		if ($object instanceof IComplete && $object->complete()->isOpen()) {
  			return false;
  		} // if
  		
  		if ($object->getState() <= STATE_ARCHIVED) {
  			return false;
  		} // if

  		$object->state()->archive();
  	} // actionMoveToArchive

    /**
     * Restpre objects from archive
     *
     * @param ApplicationObject $object
     * @param array $variables
     * @return bool
     */
    function actionRestoreFromArchive(&$object, $variables) {
      if (!($object instanceof IState)) {
        return false;
      } // if

      if (!$object->state()->canUnarchive($this->user)) {
        return false;
      } // if

      if ($object->getState() != STATE_ARCHIVED) {
        return false;
      } // if

      $object->state()->unarchive();
    } // actionRestoreFromArchive
  	
    /**
     * Return array or property => value pairs that describes this object
     *
     * $user is an instance of user who requested description - it's used to get
     * only the data this user can see
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @return array
     */
    function describe(IUser $user, $detailed = false, $for_interface = false) {
    	if ($this->actions === false) {
    		$this->rebuildActions();
    	} // if
    	
      return $this->actions;
    } // describe
    
  }