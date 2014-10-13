<?php

  /**
   * ProjectObjectTemplate class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class ProjectObjectTemplate extends BaseProjectObjectTemplate implements IRoutingContext {

	  /**
	   * Permission name
	   *
	   * @var string
	   */
	  protected $permission_name = null;

	  /**
	   * List of protected fields (can't be set using setAttributes() method)
	   *
	   * @var array
	   */
	  protected $protect = array(
		  'id',
		  'type',
		  'subtype',
		  'template_id',
		  'value',
		  'file_size'
	  );

	  /**
	   * Define fields used by this project object template
	   *
	   * @var array
	   */
	  protected $fields = array(
		  'id',
		  'type',
		  'subtype',
		  'template_id',
		  'parent_id',
		  'value',
		  'position',
		  'file_size'
	  );

	  /**
	   * Construct project object template
	   *
	   * @param mixed|null $object_type
	   */
	  function __construct($object_type) {
		  parent::__construct();
		  $this->setType(ucfirst(strtolower($object_type)));
	  } // __construct

	  // ---------------------------------------------------
	  //  Getters and Setters
	  // ---------------------------------------------------

	  /**
	   * Serialize array values and set as value
	   *
	   * @param array $values
	   */
	  function setValues(Array $values) {
		  if (is_foreachable($values)) {
			  foreach ($values as $key => &$value) {
				  if (is_string($value)) {
					  $value = trim($value);
				  } // if
			  } // foreach
		  } // if
		  parent::setValue(serialize($values));
	  } // setValues

	  /**
	   * Un serialize value and return array of values
	   *
	   * @return array|string
	   */
	  function getValues() {
		  return (array) unserialize(parent::getValue());
	  } // getValues

	  /**
	   * Get value by key
	   *
	   * @param $key
	   * @return mixed
	   */
	  function getValue($key) {
		  return array_var($this->getValues(), $key);
	  } // getValue

	  /**
	   * Set new value by key
	   *
	   * @param $key
	   * @param $value
	   */
	  function setValue($key, $value) {
		  $values = $this->getValues();
		  $values[$key] = trim($value);
		  $this->setValues($values);
	  } // setValue

	  /**
	   * Get body value start_on
	   *
	   * @return integer
	   */
	  function getStartOn() {
		  return $this->getValue('start_on');
	  } // getStartOn

	  /**
	   * Get body value due_on
	   *
	   * @return integer
	   */
	  function getDueOn() {
		  return $this->getValue('due_on');
	  } // getDueOn

	  // ---------------------------------------------------
	  //  Attribute manipulation
	  // ---------------------------------------------------

	  /**
	   * Return formatted priority
	   *
	   * @param Language $language
	   * @return string
	   */
	  /*function getFormattedPriority($language = null) {
		  switch($this->getPriority()) {
			  case PRIORITY_LOWEST:
				  return lang('Lowest', null, true, $language);
			  case PRIORITY_LOW:
				  return lang('Low', null, true, $language);
			  case PRIORITY_HIGH:
				  return lang('High', null, true, $language);
			  case PRIORITY_HIGHEST:
				  return lang('Highest', null, true, $language);
			  default:
				  return lang('Normal', null, true, $language);
		  } // switch
	  }*/ // getFormattedPriority

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
		  $result = parent::describe($user, $detailed, $for_interface);

		  $result = array_merge($result, $this->getValues());

		  $result['other_assignees'] = null;
		  $raw_other_assignee_ids = $this->getValue('other_assignees');
		  if (is_foreachable($raw_other_assignee_ids)) {
			  foreach ($raw_other_assignee_ids as $row_assignee_id) {
				  $result['other_assignees'][] = array('id' => $row_assignee_id);
			  } // foreach
		  } // if

		  $result['estimate'] = null;

		  switch ($this->getType()) {
			  case 'Milestone':
				  $start_on = $this->getStartOn();
				  $due_on = $this->getDueOn();
				  $name_suffix = '';
				  if ($start_on) {
					  if ($due_on) {
						  $name_suffix = " " . lang("(:start_on. - :due_on. day)", array('start_on'=>$start_on,'due_on'=>$due_on));
					  } // if
				  } else {
					  if ($due_on) {
						  $name_suffix = " " . lang("(0. - :due_on. day)", array('start_on'=>$start_on,'due_on'=>$due_on));
					  } // if
				  }
				  $result['name_suffix'] = $name_suffix;
				  $result['permissions'] = array(
					  'can_assign' => $this->canAssign($user),
					  'can_move' => $this->canMove($user),
					  'can_copy' => $this->canCopy($user),
					  'can_edit' => $this->canEdit($user),
					  'can_delete' => $this->canDelete($user),
				  );
				  $result['events_name'] = array(
					  'created' => 'milestone_template_created',
					  'updated' => 'milestone_template_updated'
				  );
				  break;
			  case 'Task':
				  $result['permissions'] = array(
					  'can_assign' => $this->canAssign($user),
					  'can_move' => $this->canMove($user),
					  'can_copy' => $this->canCopy($user),
					  'can_edit' => $this->canEdit($user),
					  'can_delete' => $this->canDelete($user),
				  );
				  $result['events_name'] = array(
					  'created' => 'task_template_created',
					  'updated' => 'task_template_updated'
				  );
				  $result['estimate'] = array(
						'value'           => (float) $this->getValue('estimate_value'),
						'job_type_id'     => $this->getValue('estimate_job_type_id'),
				  );
				  break;
			  case "Subtask":
				  $result['name'] = $this->getValue('body');
				  $result['permissions'] = array(
					  'can_assign' => $this->canAssign($user),
					  'can_move' => $this->canMove($user),
					  'can_copy' => $this->canCopy($user),
					  'can_edit' => $this->canEdit($user),
					  'can_delete' => $this->canDelete($user),
				  );
				  $result['estimate'] = array(
					  'value'           => (float) $this->getValue('estimate_value'),
					  'job_type_id'     => $this->getValue('estimate_job_type_id'),
				  );
					$subtask_parent_id = $this->getParentId();
					if ($subtask_parent_id) {
						$result['parent'] = array(
							'class' => 'task',
							'id' => $subtask_parent_id
						);
					} // if
				  break;
			  case 'Position':
				  $assigned = Users::findById($this->getValue('user_id'));
				  $result['assigned'] = $assigned instanceof User ?
					  array(
						  'id'      => $assigned->getId(),
						  'name'    => $assigned->getName(),
						  'avatar'  => $assigned->avatar()->getUrl(IUserAvatarImplementation::SIZE_BIG)
					  ) : false;
				  $result['permissions'] = array(
					  'can_assign' => $this->canAssign($user),
					  'can_edit' => $this->canEdit($user),
					  'can_delete' => $this->canDelete($user)
				  );
				  $result['company_id'] = 0;
				  $result['events_name'] = array(
					  'created' => 'position_template_created',
					  'updated' => 'position_template_updated'
				  );
				  break;
			  case "Category":
				  $result['subtype'] = $this->getSubtype();
				  break;
			  case "File":
				  $result['icon'] = get_file_icon_url($this->getValue('name'), "48x48");
				  $result['file_size'] = $this->getFileSize() ? format_file_size($this->getFileSize()) : 0;
				  $result['permissions'] = array(
					  'can_delete' => $this->canDelete($user)
				  );
				  break;
		  } // switch

		  $result['template_id'] = $this->getTemplateId();

		  if($detailed) {
			  $result['template'] = $this->getTemplate() instanceof ProjectTemplate ? $this->getTemplate()->describe($user, false, $for_interface) : null;
		  } // if

		  return $result;
	  } // describe

	  // ---------------------------------------------------
	  //  Permissions
	  // ---------------------------------------------------

	  /**
	   * Check if specific user can assign position
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canAssign(User $user) {
			return (($user->isAdministrator() || $user->isProjectManager()) && $this->getType() == 'Position') ? true : false;
		} // canAssign

	  /**
	   * Check if specific user can move this object
	   *
	   * @param User $user
	   * @return boolean
	   */
	  function canMove(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canMove

	  /**
	   * Return true if specific user can copy this object
	   *
	   * @param User $user
	   * @return boolean
	   */
	  function canCopy(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canCopy

	  /**
	   * Can user edit object
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canEdit(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canEdit

	  /**
	   * Can user delete object
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canDelete(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canDelete

	  // ---------------------------------------------------
	  //  Relations
	  // ---------------------------------------------------

	  /**
	   * Set parent template
	   *
	   * @param ProjectTemplate $template
	   * @return ProjectTemplate
	   */
	  function setTemplate(ProjectTemplate $template) {
		  if($template instanceof ProjectTemplate) {
			  $this->setTemplateId($template->getId());
			  $this->routing_context_params = false;
		  } else {
			  throw new InvalidInstanceError('project_template', $template, 'Template');
		  } // if

		  return $this->getTemplate();
	  } // setProject

	  /**
	   * Return parent template
	   *
	   * @return ProjectTemplate
	   */
	  function &getTemplate() {
		  return DataObjectPool::get('ProjectTemplate', $this->getTemplateId());
	  } // getTemplate

	  // ---------------------------------------------------
	  //  Interface implementations
	  // ---------------------------------------------------

	  /**
	   * Routing context name
	   *
	   * @var string
	   */
	  private $routing_context = false;

	  /**
	   * Return routing context name
	   *
	   * @return string
	   */
	  function getRoutingContext() {
		  if($this->routing_context === false) {
			  $this->routing_context = Inflector::underscore(get_class($this));
		  } // if

		  return $this->routing_context;
	  } // getRoutingContext

	  /**
	   * Routing context parameters
	   *
	   * @var array
	   */
	  private $routing_context_params = false;

	  /**
	   * Return routing context parameters
	   *
	   * @return mixed
	   */
	  function getRoutingContextParams() {
		  if($this->routing_context_params === false) {
			  $this->routing_context_params = array(
				  'template_id' => $this->getTemplateId(),
				  'object_type' => strtolower($this->getType()),
				  'object_id' => $this->getId()
			  );
		  } // if

		  return $this->routing_context_params;
	  } // getRoutingContextParams

	  // ---------------------------------------------------
	  //  System
	  // ---------------------------------------------------

	  /**
	   * Validate presence of values
	   *
	   * @param string $field
	   * @param null $min_value
	   * @return bool
	   */
	  function validatePresenceOf($field, $min_value = null) {
		  $value = $this->getValue($field);

		  if (is_string($value) && !is_numeric($value)) {
			  if (trim($value)) {
				  return $min_value === null ? true : (strlen_utf(trim($value)) >= $min_value);
			  } else {
					return false;
			  } // if
		  } else {
				if (!empty($value)) {
					return $min_value === null ? true : ($value >= $min_value);
				} else {
					return false;
				} // if
		  } // if
	  } // validatePresenceOf

	  /**
	   * Validate before save
	   *
	   * @param ValidationErrors $errors
	   */
	  function validate(ValidationErrors &$errors) {
		  switch($this->getType()) {
			  case "Milestone":
					$specify = (boolean) $this->getValue('specify');
					if ($specify) {
						$start_on = $this->getStartOn();
						$due_on = $this->getDueOn();
						if (!is_numeric($start_on) || !is_numeric($due_on)) {
							$errors->addError(lang('Milestone start day or due day need to be valid number'), 'date_range');
						} else {
							if ($start_on <= 0) {
								$errors->addError(lang('Milestone start day must be greater than zero'), 'date_range');
							} //if

							if ($due_on < $start_on) {
								$errors->addError(lang('Milestone due day must be greater than start day'), 'date_range');
							} //if
						} // if
					} // if

				  if(!$this->validatePresenceOf('name')) {
					  $errors->addError(lang('Title is required'), 'name');
				  } // if
				  break;
			  case "Position":
				  if(!$this->validatePresenceOf('name')) {
					  $errors->addError(lang('Title is required'), 'name');
				  } // if
				  break;
			  case "Category":
					$categories = ProjectObjectTemplates::findByType($this->getTemplate(), 'Category');
					if (is_foreachable($categories)) {
						foreach ($categories as $category) {
							if ($category instanceof ProjectObjectTemplate && $category->getValue('name') == $this->getValue('name') && $category->getSubtype() == $this->getSubtype()) {
								$errors->addError(lang('Category name must be unique'), 'name');
								break;
							} // if
						} // foreach
					} // if
			  case "Task":
				  $specify = (boolean) $this->getValue('specify');
				  if ($specify) {
					  $due_on = $this->getDueOn();
					  if (!is_numeric($due_on)) {
						  $errors->addError(lang('Task due day need to be valid number'), 'due_on');
					  } elseif ($due_on <= 0) {
						  $errors->addError(lang('Task due day must be greater than zero'), 'date_range');
					  } //if
				  } // if

			    if (!$this->validatePresenceOf('name')) {
					  $errors->addError(lang('Title is required.'), 'name');
				  } // if
				  break;
			  case "Subtask":
				  $specify = (boolean) $this->getValue('specify');
				  if ($specify) {
					  $due_on = $this->getDueOn();
					  if (!is_numeric($due_on)) {
						  $errors->addError(lang('Subtask due day need to be valid number'), 'due_on');
					  } elseif ($due_on <= 0) {
						  $errors->addError(lang('Subtask due day must be greater than zero'), 'date_range');
					  } //if
				  } // if

				  if(!$this->validatePresenceOf('body')) {
					  $errors->addError(lang('Title is required.'), 'body');
				  } // if
				  break;
		  } // switch

		  parent::validate($errors, true);
	  } // validate

	  /**
	   * Save project object template to database
	   *
	   * @return boolean
	   */
	  function save() {
		  if (!$this->isLoaded()) {
			  $next_position = DB::executeFirstCell('SELECT MAX(position) FROM ' . TABLE_PREFIX . 'project_object_templates WHERE type = ? AND template_id = ?', $this->getType(), $this->getTemplateId()) + 1;

			  $this->setPosition($next_position);
		  } // if

		  return parent::save();
	  } // save

	  /**
	   * Delete project object template
	   *
	   * @return bool
	   */
	  function delete() {
		  if ($this->getType() == "File") {
			  @unlink(UPLOAD_PATH . '/' . $this->getValue('location'));
		  } // if

		  // if Milestone or Task then do:
			ProjectObjectTemplates::deleteChildrenByParent($this);

		  return parent::delete();
	  } // delete
  }