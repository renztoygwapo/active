<?php

  /**
   * ProjectTemplate class
   *
   * @package ActiveCollab.modules.system
   * @subpackage models
   */
  class ProjectTemplate extends BaseProjectTemplate  implements IAvatar, IRoutingContext, IProjectBasedOn {

	  /**
	   * Permission name
	   *
	   * @var string
	   */
	  protected $permission_name = 'project_template';

	  /**
	   * Define fields used by this object
	   *
	   * @var array
	   */
	  protected $fields = array(
		  'id',
		  'category_id',
		  'company_id',
		  'name',
		  'created_on',
		  'created_by_id',
		  'created_by_name',
		  'created_by_email'
	  );

	  /**
	   * Construct ProjectTemplate
	   *
	   * @param integer $id
	   * @return ProjectTemplate
	   */
	  function __construct($id = null) {
		  parent::__construct($id);
	  } // __construct

	  /**
	   * Can build project from this template
	   *
	   * @param User $user
	   * @return bool
	   */
	  function canBuild(User $user) {
		  return $user instanceof User && ($user->isAdministrator() || $user->isProjectManager());
	  } // canBuild

	  // ---------------------------------------------------
	  //  Implementation
	  // ---------------------------------------------------

	  /**
	   * ProjectTemplate positions implementation instance for this object
	   *
	   * @var IProjectTemplatePositionsImplementation
	   */
	  private $positions;

	  /**
	   * Return ProjectTemplate positions implementation for this object
	   *
	   * @return IProjectTemplatePositionsImplementation
	   */
	  function positions()
	  {
		  if (empty($this->positions)) {
			  $this->positions = new IProjectTemplatePositionsImplementation($this);
		  } // if

		  return $this->positions;
	  } // avatar

	  /**
	   * Template avatar implementation instance for this object
	   *
	   * @var IProjectTemplateAvatarImplementation
	   */
	  private $avatar;

	  /**
	   * Return avatar implementation for this object
	   *
	   * @return IProjectTemplateAvatarImplementation
	   */
	  function avatar()
	  {
		  if (empty($this->avatar)) {
			  $this->avatar = new IProjectTemplateAvatarImplementation($this);
		  } // if

		  return $this->avatar;
	  } // avatar

	  /**
	   * Get type
	   *
	   * @return string
	   */
	  function getType() {
		  return "Template";
	  }

	  /**
	   * Return company instance
	   *
	   * @return Company
	   */
	  function getCompany() {
		  $company_id = $this->getCompanyId();

		  if($company_id && DataObjectPool::get('Company', $company_id) instanceof Company) {
			  return DataObjectPool::get('Company', $company_id);
		  } else {
			  return Companies::findOwnerCompany();
		  } // if
	  } // getCompany

	  // ---------------------------------------------------
	  //  Permissions
	  // ---------------------------------------------------

	  /**
	   * Can view template
	   *
	   * @param User $user
	   * @return boolean
	   */
	  function canView(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canView

	  /**
	   * Can edit template properties
	   *
	   * @param User $user
	   * @return boolean
	   */
	  function canEdit(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canEdit

	  /**
	   * Can delete template properties
	   *
	   * @param User $user
	   * @return boolean
	   */
	  function canDelete(User $user) {
		  return ($user->isAdministrator() || $user->isProjectManager()) ? true : false;
	  } // canDelete

	  /**
	   * Check if is one or more object of this template scheduled
	   * @return bool
	   */
	  function isScheduled() {
		  return (boolean) ProjectObjectTemplates::getTotalOfScheduledObjectsByTemplate($this);
	  } // isScheduled

	  // ---------------------------------------------------
	  //  Interface implementations
	  // ---------------------------------------------------

	  /**
	   * Return routing context name
	   *
	   * @return string
	   */
	  function getRoutingContext() {
		  return 'project_template';
	  } // getRoutingContext

	  /**
	   * Return routing context parameters
	   *
	   * @return mixed
	   */
	  function getRoutingContextParams() {
		  return array(
			  'template_id' => $this->getId(),
		  );
	  } // getRoutingContextParams

	  // ---------------------------------------------------
	  //  System
	  // ---------------------------------------------------

	  /**
	   * Validate before save
	   *
	   * @param ValidationErrors $errors
	   */
	  function validate(ValidationErrors &$errors) {
		  if(!$this->validatePresenceOf('name')) {
			  $errors->addError(lang('Template name is required'), 'name');
		  } // if

		  parent::validate($errors, true);
	  } // validate

	  /**
	   * Copy template objects to destination project
	   *
	   * @param Project $to
	   * @throws Exception
	   */
	  public function copyItems(Project $to, $positions=null) {
		  try {
			  DB::beginWork('Copying project items @ ' . __CLASS__);

			  $logged_user = Authentication::getLoggedUser();

			  // project start on date
			  if ($this->isScheduled()) {
				  $project_starts_on = DateValue::makeFromString(ConfigOptions::getValueFor('first_milestone_starts_on', $to));
				  if (!($project_starts_on instanceof DateValue)) {
					  throw new Error(lang('Please specify date when project starts'));
				  } // if
			  } else {
				  $project_starts_on = null;
			  } // if

			  if ($project_starts_on instanceof DateValue) {
				  while ($project_starts_on->isWeekend()) {
					  $project_starts_on->advance(86400);
				  } // while
			  } // if

			  $now = new DateTimeValue();

			  $position_templates = ProjectObjectTemplates::findByType($this, 'Position');
			  $project_permissions = array();
			  if (is_foreachable($position_templates)) {
				  foreach ($position_templates as $position_template) {
					  if ($position_template instanceof ProjectObjectTemplate) {
						  $project_permissions[$position_template->getId()] = $position_template->getValue('project_template_permissions');
					  } // if
				  } // foreach
			  } // if

			  // copy users
			  $users_map = array();
			  if (is_foreachable($positions)) {
				  foreach ($positions as $position_id => $user_id) {
					  $user = Users::findById($user_id);
					  if ($user instanceof User) {
						  if (isset($project_permissions[$position_id])) {
							  $role = ProjectRoles::findById(array_var($project_permissions[$position_id], 'role_id', 0));
							  $permissions = array_var($project_permissions[$position_id], 'permissions', null);
							  $to->users()->add($user, $role, $permissions);
						  } else {
							  $to->users()->add($user);
						  } // if
						  $users_map[$position_id] = $user;
					  }	// if
				  } // foreach
			  } // if

			  // copy milestones
			  $milestone_templates = ProjectObjectTemplates::findByType($this, "Milestone");
			  $milestone_map = array();
			  if (is_foreachable($milestone_templates)) {
				  foreach ($milestone_templates as $milestone_template) {
					  if ($milestone_template instanceof ProjectObjectTemplate) {
						  $assigned_user = $users_map[$milestone_template->getValue('assignee_id')];
						  $assignee_id = $assigned_user instanceof User ? $assigned_user->getId() : 0;

						  $other_assignees = array();
						  $other_assigned_users = array();
						  $position_ids = $milestone_template->getValue('other_assignees');
						  if (is_foreachable($position_ids)) {
							  foreach ($position_ids as $position_id) {
								  $user = $users_map[$position_id];
								  if ($user instanceof User && $user->getId() != $assignee_id) {
									  array_push($other_assignees, $user->getId());
									  $other_assigned_users[] = $user;
								  } // if
							  } // foreach
						  } // if

						  // prepare milestone data
						  $milestone_data = $milestone_template->getValues();
						  $start_on = (integer) $milestone_template->getStartOn();
						  $due_on = (integer) $milestone_template->getDueOn();

						  $milestone_data = array_merge($milestone_data, array(
							  'assignee_id'       => $assignee_id,
							  'other_assignees'   => $other_assignees
						  ));

						  $milestone = new Milestone();
						  $milestone->setAttributes($milestone_data);
						  $milestone->setProjectId($to->getId());
						  $milestone->setState($to->getState());
						  $milestone->setPosition($milestone_template->getPosition());
						  if ($project_starts_on instanceof DateValue && $start_on > 0 && $due_on > 0) {
							  // starts on
							  $i = 0;
							  $day = 0;
							  $real_start_date = null;
							  while ($i < $start_on) {
								  $real_start_date = $project_starts_on->advance($day * 86400, false);
								  if (!$real_start_date->isWeekend()) {
									  $i++;
								  } // if
								  $day++;
							  } // while

							  $diff = $due_on - ($start_on - 1);
							  $i = 0;
							  $day = 0;
							  $real_due_date = null;
							  while ($i < $diff) {
								  $real_due_date = $real_start_date->advance($day * 86400, false);
								  if (!$real_due_date->isWeekend()) {
									  $i++;
								  } // if
								  $day++;
							  } // while

							  if ($real_start_date instanceof DateValue && $real_due_date instanceof DateValue) {
								  $milestone->setStartOn($real_start_date);
								  $milestone->setDueOn($real_due_date);
							  } // if
						  } // if
						  $milestone->setVisibility(VISIBILITY_NORMAL);
						  $milestone->setCreatedBy($logged_user);
						  $milestone->setCreatedOn($now);
						  $milestone->save();

						  // Subscribe to milestone
						  $milestone->subscriptions()->subscribe($to->getLeader());
						  if ($assigned_user instanceof User) {
							  $milestone->subscriptions()->subscribe($assigned_user);
						  } // if
						  if (is_foreachable($other_assigned_users)) {
							  foreach($other_assigned_users as $other_assigned_user) {
								  if ($other_assigned_user instanceof User) {
									  $milestone->subscriptions()->subscribe($other_assigned_user);
								  } // if
							  } // foreach
						  } // if

						  // map milestone template id with new milestone object
						  $milestone_map[$milestone_template->getId()] = $milestone;
					  } // if
				  } // foreach
			  } // if

			  // copy categories
			  $category_templates = ProjectObjectTemplates::findByType($this, "Category");
			  $task_categories_map = array();
			  $file_categories_map = array();
			  if (is_foreachable($category_templates)) {
				  $existing_task_categories = $to->availableCategories()->get('TaskCategory');
				  $existing_discussion_categories = $to->availableCategories()->get('DiscussionCategory');
				  $existing_file_categories = $to->availableCategories()->get('AssetCategory');
				  foreach ($category_templates as $category_template) {
					  if ($category_template instanceof ProjectObjectTemplate) {
						  $cat_context = ucfirst(strtolower($category_template->getSubtype()));
						  if ($cat_context == 'File') {
							  $cat_context = 'Asset';
						  } // if
						  $category_type = $cat_context . 'Category';

						  $temp_category_name = $category_template->getValue('name');

						  // If exist task category map it and continue
						  if (is_foreachable($existing_task_categories)) {
							  foreach($existing_task_categories as $existing_task_category) {
								  if ($existing_task_category instanceof TaskCategory && $existing_task_category->getName() == $temp_category_name) {
									  $task_categories_map[$existing_task_category->getId()] = $existing_task_category;
									  continue;
								  } // if
							  } // foreach
						  } // if

						  // If exist discussion category just continue
						  if (is_foreachable($existing_discussion_categories)) {
							  foreach ($existing_discussion_categories as $existing_discussion_category) {
								  if ($existing_discussion_category instanceof DiscussionCategory && $existing_discussion_category->getName() == $temp_category_name) {
									  continue;
								  } // if
							  } // foreach
						  } // if

						  // If exist file category just continue
						  if (is_foreachable($existing_file_categories)) {
							  foreach ($existing_file_categories as $existing_file_category) {
								  if ($existing_file_category instanceof AssetCategory && $existing_file_category->getName() == $temp_category_name) {
									  continue;
								  } // if
							  } // foreach
						  } // if

						  // If don't exist discussion nor task category create it
						  if (class_exists($category_type)) {
							  $category = new $category_type();
							  if ($category instanceof ProjectObjectCategory) {
								  $category->setName($category_template->getValue('name'));
								  $category->setParent($to);
								  $category->setCreatedBy($logged_user);
								  $category->setCreatedOn($now);
								  $category->save();
							  } // if
						  } // if

						  // map category template id with new category object
						  if ($category instanceof TaskCategory) {
							  $task_categories_map[$category_template->getId()] = $category;
						  } elseif ($category instanceof AssetCategory) {
							  $file_categories_map[$category_template->getId()] = $category;
						  } // if
					  } // if
				  } // foreach
			  } // if

			  // copy tasks
			  $task_templates = ProjectObjectTemplates::findByType($this, "Task");
			  $task_templates_map = array();
			  $task_map = array();
			  if (is_foreachable($task_templates)) {
				  foreach ($task_templates as $task_template) {
					  if ($task_template instanceof ProjectObjectTemplate) {

						  $assigned_user = $users_map[$task_template->getValue('assignee_id')];
						  $assignee_id = $assigned_user instanceof User ? $assigned_user->getId() : 0;

						  $other_assignees = array();
						  $other_assigned_users = array();
						  $position_ids = $task_template->getValue('other_assignees');
						  if (is_foreachable($position_ids)) {
							  foreach ($position_ids as $position_id) {
								  $user = $users_map[$position_id];
								  if ($user instanceof User && $user->getId() != $assignee_id) {
									  array_push($other_assignees, $user->getId());
									  $other_assigned_users[] = $user;
								  } // if
							  } // foreach
						  } // if

						  $milestone = isset($milestone_map[$task_template->getParentId()]) ? $milestone_map[$task_template->getParentId()] : null;
						  $task_category = isset($task_categories_map[$task_template->getValue('category_id')]) ? $task_categories_map[$task_template->getValue('category_id')] : null;

						  // prepare task data
						  $task_data = $task_template->getValues();
						  $due_on = (integer) $task_template->getDueOn();

						  unset($task_data['milestone_id']);
						  $task_data = array_merge($task_data, array(
							  'assignee_id'     => $assignee_id,
							  'other_assignees' => $other_assignees
						  ));

						  $task = new Task();
						  $task->setAttributes($task_data);
						  $task->setProject($to);
						  $task->setMilestoneId($milestone instanceof Milestone ? $milestone->getId() : null);
						  $task->setState($to->getState());
						  $task->setPosition($task_template->getPosition());
						  if ($project_starts_on instanceof DateValue && $due_on > 0) {
							  $reliable_date = $project_starts_on->advance(0, false);

							  if ($milestone instanceof Milestone) {
								  $milestone_start_on = $milestone->getStartOn();
								  if ($milestone_start_on instanceof DateValue) {
									  $reliable_date = $milestone_start_on->advance(0, false);
								  } // if
							  } // if

							  $real_due_date = null;

							  $i = 0;
							  $day = 0;
							  while ($i < $due_on) {
								  $real_due_date = $reliable_date->advance($day * 86400, false);
								  if (!$real_due_date->isWeekend()) {
									  $i++;
								  } // if
								  $day++;
							  } // while

							  if ($real_due_date instanceof DateValue) {
								  $task->setDueOn($real_due_date);
							  } // if
						  } // if
						  $task->setCreatedBy($logged_user);
						  $task->setCategoryId($task_category instanceof TaskCategory ? $task_category->getId() : null);
						  $task->setCreatedOn($now);
						  $task->save();

						  // Task subscriptions
						  $task->subscriptions()->subscribe($to->getLeader());
						  if ($assigned_user instanceof User) {
							  $task->subscriptions()->subscribe($assigned_user);
						  } // if
						  if (is_foreachable($other_assigned_users)) {
							  foreach ($other_assigned_users as $other_assigned_user) {
								  if ($other_assigned_user instanceof User) {
									  $task->subscriptions()->subscribe($other_assigned_user);
								  } // if
							  } // foreach
						  } // if

						  // set tracking
						  if(AngieApplication::isModuleLoaded('tracking')) {
							  $estimated_time = (float) $task_template->getValue('estimate_value');

							  if($estimated_time) {
								  $task->tracking()->setEstimate($estimated_time, $task_template->getValue('estimate_job_type_id'), null, $logged_user);
							  } // if
						  } // if

						  // map task template id with new task object

						  $task_map[$task_template->getId()] = $task;
						  $task_templates_map[$task_template->getId()] = $task_template;
					  } // if
				  } // foreach
			  } // if

			  // copy subtasks
			  $subtask_templates = ProjectObjectTemplates::findByType($this, "Subtask");
			  if (is_foreachable($subtask_templates)) {
				  foreach ($subtask_templates as $subtask_template) {
					  if ($subtask_template instanceof ProjectObjectTemplate) {

						  $subtask_data = $subtask_template->getValues();

						  $assigned_user = $users_map[$subtask_template->getValue('assignee_id')];
						  $subtask_data = array_merge($subtask_data, array(
							  'assignee_id' => $assigned_user instanceof User ? $assigned_user->getId() : 0
						  ));

						  $due_on = (integer) $subtask_template->getDueOn();

						  $task_template = isset($task_templates_map[$subtask_template->getParentId()]) ? $task_templates_map[$subtask_template->getParentId()] : null;
						  if ($task_template instanceof ProjectObjectTemplate) {
							  $milestone = isset($milestone_map[$task_template->getParentId()]) ? $milestone_map[$task_template->getParentId()] : null;
						  } else {
							  $milestone = null;
						  } // if
						  $task = isset($task_map[$subtask_template->getParentId()]) ? $task_map[$subtask_template->getParentId()] : null;

						  if ($task instanceof Task) {
							  $subtask = new ProjectObjectSubtask();
							  $subtask->setAttributes($subtask_data);
							  if ($project_starts_on instanceof DateValue && $due_on > 0) {
								  $reliable_date = $project_starts_on->advance(0, false);

								  if ($milestone instanceof Milestone) {
									  $milestone_start_on = $milestone->getStartOn();
									  if ($milestone_start_on instanceof DateValue) {
										  $reliable_date = $milestone_start_on->advance(0, false);
									  } // if
								  } // if

								  $i = 0;
								  $day = 0;
								  while ($i < $due_on) {
									  $real_due_date = $reliable_date->advance($day * 86400, false);
									  if (!$real_due_date->isWeekend()) {
										  $i++;
									  } // if
									  $day++;
								  } // while

								  if ($real_due_date instanceof DateValue) {
									  $subtask->setDueOn($real_due_date);
								  } // if
							  } // if
							  $subtask->setParent(Tasks::findById($task->getId()));
							  $subtask->setState($to->getState());
							  $subtask_position = $subtask_template->getPosition() ? $subtask_template->getPosition() : 0;
							  $subtask->setPosition($subtask_position);
							  $subtask->setCreatedBy($logged_user);
							  $subtask->setCreatedOn($now);
							  $subtask->save();

							  $subtask->subscriptions()->subscribe($to->getLeader());
							  if ($assigned_user instanceof User) {
								  $subtask->subscriptions()->subscribe($assigned_user);
							  } // if
						  } // if
					  } // if
				  } // foreach
			  } // if

			  //copy files
			  $file_templates = ProjectObjectTemplates::findByType($this, "File");
			  if (is_foreachable($file_templates)) {
				  foreach ($file_templates as $file_template) {

					  $file_category = isset($file_categories_map[$file_template->getValue('category_id')]) ? $file_categories_map[$file_template->getValue('category_id')] : null;

					  if ($file_template instanceof ProjectObjectTemplate) {
						  $file_data = $file_template->getValues();

						  $file = new File();
						  $file->setAttributes($file_data);

//						  $file->setName($attachment->getName());
//						  $file->setBody(array_var($descriptions, $index));
//						  $file->setProject($this->active_project);
//						  $file->setSize($attachment->getSize());
//						  $file->setLocation($attachment->getLocation());
//						  $file->setMimeType($attachment->getMimeType());
//						  $file->setState(STATE_VISIBLE);

						  $file->setSize($file_template->getFileSize());
						  $file->setVisibility(VISIBILITY_NORMAL);
						  $file->setCreatedOn($now);
						  $file->setState($to->getState());
						  $file->setPosition($file_template->getPosition());
						  $file->setCategoryId($file_category instanceof AssetCategory ? $file_category->getId() : null);
						  $file->setVersionNum(1);

						  $file_copy = $file->copyToProject($to);

						  $file_copy->subscriptions()->subscribe($to->getLeader());
					  } // if
				  } // foreach
			  } // if

			  DB::commit('Project items copied @ ' . __CLASS__);
		  } catch(Exception $e) {
			  DB::rollback('Failed to copy project items @ ' . __CLASS__);
			  throw $e;
		  } // try
	  } // copyItems

	  // ---------------------------------------------------
	  //  Options
	  // ---------------------------------------------------

	  /**
	   * Prepare list of options that $user can use
	   *
	   * @param IUser $user
	   * @param NamedList $options
	   * @param string $interface
	   * @return NamedList
	   */
	  protected function prepareOptionsFor(IUser $user, NamedList $options, $interface = AngieApplication::INTERFACE_DEFAULT) {
		  if($interface == AngieApplication::INTERFACE_DEFAULT) {
			  if($this->canEdit($user)) {
				  $options->add('project_template_edit', array(
					  'text' => lang('Edit'),
					  'url' => $this->getEditUrl(),
					  'icon' => $interface == AngieApplication::INTERFACE_DEFAULT ? AngieApplication::getImageUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT) : '',
					  'onclick' => new FlyoutFormCallback('template_edited', array(
						  'success_message' => lang('Project template has been edited'),
						  'width' => '330'
					  )),
				  ), true);
			  } // if

			  if($this->canDelete($user)) {
				  $options->add('project_template_delete', array(
					  'text' => lang('Delete'),
					  'url' => $this->getDeleteUrl(),
					  'onclick' => new AsyncLinkCallback(array(
						  'confirmation' => lang('Are you sure that you want to permanently delete this project template?'),
						  'success_message' => lang('Project template has been successfully deleted'),
						  'success_event' => 'template_deleted',
					  )),
				  ), true);
			  } // if
		  } // if
	  } // prepareOptionsFor

	  /**
	   * Delete project template
	   *
	   * @return bool
	   */
	  function delete() {
		  // if Milestone or Task then do:
		  ProjectTemplates::deleteChildrenByParent($this);

		  return parent::delete();
	  } // delete
    
  }