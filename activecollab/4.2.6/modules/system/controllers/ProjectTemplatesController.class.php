<?php

// Inherit projects controller
AngieApplication::useController('projects', SYSTEM_MODULE);

/**
 * Main template request controller
 *
 * @package activeCollab.modules.system
 * @subpackage controllers
 */
class ProjectTemplatesController extends ProjectsController {

	/**
	 * Active template request
	 *
	 * @var ProjectTemplate
	 */
	protected $active_template;

	/**
	 * Categories delegate controller instance
	 *
	 * @var CategoriesController
	 */
	protected $categories_delegate;

	/**
	 * Categories delegate controller instance
	 *
	 * @var CategoriesController
	 */
	protected $positions_delegate;

	/**
	 * Whether project outline templates supports tracking
	 *
	 * @var boolean
	 */
	private $support_tracking = false;

	/**
	 * Construct template requests controller
	 *
	 * @param Request $parent
	 * @param string $context
	 */
	function __construct(Request $parent, $context = null) {
		parent::__construct($parent, $context);
	} // __construct

	/**
	 * Prepare controller
	 */
	function __before() {
		parent::__before();

		if (ProjectTemplates::canManage($this->logged_user)) {
			$templates_url = Router::assemble('project_templates');

			if ($this->request->isWebBrowser() && !$this->request->isAsyncCall()) {
				$this->wireframe->breadcrumbs->add('project_templates', 'Templates', $templates_url);
				$this->wireframe->tabs->setCurrentTab('project_templates');
			} // if

			$template_id = $this->request->getId('template_id');
			if ($template_id) {
				$this->active_template = ProjectTemplates::findById($template_id);
			} // if

			if ($this->active_template instanceof ProjectTemplate) {
				if (!$this->active_template->isAccessible()) {
					$this->response->notFound();
				} // if
			} else {
				$this->active_template = new ProjectTemplate();
			} // if

			$add_template_url = false;
			if (($this->request->isWebBrowser() || $this->request->isMobileDevice())) {
				if (ProjectTemplates::canAdd($this->logged_user)) {
					$add_template_url = Router::assemble('project_templates_add');

					$this->wireframe->actions->add('project_templates_add', lang('New Template'), $add_template_url, array(
						'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
						'onclick' => new FlyoutFormCallback('template_created', array(
							'width' => '330',
							'height' => '300'
						)),
						'primary' => true
					));
				} // if
			} // if

			$this->response->assign(array(
				'active_template' => $this->active_template,
				'add_template_url' => $add_template_url,
			));

			if ($this->activity_logs_delegate instanceof ActivityLogsController) {
				$this->activity_logs_delegate->__setProperties(array(
					'show_activities_in' => &$this->active_template
				));
			} // if

			if (AngieApplication::isModuleLoaded('tracking')) {
				$this->support_tracking = true;
			} // if
		} else {
			$this->response->forbidden();
		} // if
	} // __before

	/**
	 * Show active templates
	 */
	function index() {
    if($this->request->isApiCall()) {
      $id_name_map = ProjectTemplates::getIdNameMap();

      if($id_name_map) {
        $result = array();
        $template_url = Router::assemble('project_template', array('template_id' => '--TEMPLATE-ID--'));

        foreach($id_name_map as $id => $name) {
          $result[] = array(
            'id' => $id,
            'name' => $name,
            'scheduled_items' => ProjectObjectTemplates::getTotalOfScheduledObjectsByTemplate($id),
            'permalink' => str_replace('--TEMPLATE-ID--', $id, $template_url),
          );
        } // foreach
      } else {
        $result = null;
      } // if

      $this->response->respondWithData($result, array(
        'as' => 'project_templates',
      ));
    } else {
      $this->wireframe->javascriptAssign('reorder_templates_url', Router::assemble('project_templates_reorder', array('template_id' => $this->active_template->getId())));
      $this->response->assign('templates', ProjectTemplates::findForObjectsList($this->logged_user));
    } // if
	} // index

	/**
	 * Update project template positions on shelf
	 */
	function reorder() {
		if($this->request->isAsyncCall() && $this->request->isSubmitted()) {
			$new_order = $this->request->post('new_order');
			if(empty($new_order)) {
				$this->response->badRequest();
			} //if
			$new_order = explode(",", $new_order);

			try {
				DB::beginWork('Updating project templates position @ ' . __CLASS__);

				$position = 1;
				foreach($new_order as $template_id) {
					DB::execute('UPDATE ' . TABLE_PREFIX . 'project_templates SET position = ? WHERE id = ?', $position, $template_id);
					$position++;
				}//foreach

				DB::commit('Project templates position updated @ ' . __CLASS__);

				$this->response->ok();
			} catch(Exception $e) {
				DB::rollback('Failed to update project templates position @ ' . __CLASS__);

				$this->response->exception($e);
			} // try
		} else {
			$this->response->badRequest();
		} // if
	} // reorder

	/**
	 * Show template overview
	 */
	function view() {
		if ($this->active_template->isLoaded()) {
			if ($this->active_template->canEdit($this->logged_user)) {

				// use template outline widget
				AngieApplication::useWidget('template_outline', SYSTEM_MODULE);
				AngieApplication::useWidget('select_project_permissions', SYSTEM_MODULE);

				$this->wireframe->setPageObject($this->active_template, $this->logged_user);

				// extract labels in needed form
				$labels = array();
				$label_types = array();
				$default_labels = array();

				EventsManager::trigger('on_label_types', array(&$label_types));

				if (is_foreachable($label_types)) {
					foreach ($label_types as $label_type => $label_info) {
						$current_labels = Labels::findByType($label_type);
						$current_default_label = Labels::findDefault($label_type);
						if ($current_default_label instanceof Label) {
							$default_labels[$label_type] = $current_default_label->getId();
						} // if
						if (is_foreachable($current_labels)) {
							foreach ($current_labels as $current_label_id => $current_label) {
								$labels[$label_type][$current_label->getId()] = $current_label->describe($this->logged_user, false, true);
							} // foreach
						} // if
					} // foreach
				} // if

				// add urls
				$add_urls = array(
					'milestone' => Router::assemble('project_object_template_add', array('template_id' => $this->active_template->getId(), 'object_type' => 'milestone')),
					'task' => Router::assemble('project_object_template_add', array('template_id' => $this->active_template->getId(), 'object_type' => 'task', 'parent_id' => '--PARENT-ID--')),
					'task_subtask' => Router::assemble('project_object_template_add', array('template_id' => $this->active_template->getId(), 'object_type' => 'subtask', 'parent_id' => '--PARENT-ID--')),
				);

				// permissions
				$permissions = array(
					'can_add_milestones' => true,
					'can_see_tasks' => true,
					'can_add_tasks' => true,
					'can_manage_tasks' => true,
					'can_see_todolists' => false,
					'can_add_todolists' => false,
					'can_manage_todolists' => false
				);

				if ($this->support_tracking) {
					$permissions['can_use_tracking'] = true;
				} // if

				$unclassified_label = lang('Unclassified Tasks');

				// find user map
				$positions = $this->active_template->positions()->get(); // without cache
				$positions_map = array(); //
				$companies_map = array(lang("All Positions"));
				if (is_foreachable($positions)) {
					foreach ($positions as $position) {
						$positions_map[0][$position->getId()] = $position->getValue('name');
					} // foreach
				} // if

				// Milestones
				$milestones = ProjectObjectTemplates::findObjectsForOutline($this->active_template);
				// Milestones IdNameMap
				$milestone_map = array();
				if (is_foreachable($milestones)) {
					foreach ($milestones as $milestone) {
						$milestone_map[array_var($milestone, 'id')]	= array_var($milestone, 'name');
					} // foreach
				} // if

				// Task Categories
				$task_categories = ProjectObjectTemplates::findCategoriesForList($this->active_template, "task");
				// Task Categories IdNameMap
				$task_category_map = array();
				if (is_foreachable($task_categories)) {
					foreach ($task_categories as $task_category) {
						$task_category_map[array_var($task_category, 'id')]	= array_var($task_category, 'name');
					} // foreach
				} // if

				$this->response->assign(array(
					'default_visibility'      => false,
					'initial_subobjects'      => JSON::encode($milestones),
					'users'                   => array(),
					'labels_map'              => $labels,
					'default_labels'          => $default_labels,
					'milestones_map'          => $milestone_map,
					'priorities_map'          => array('2' => lang('Highest'), '1' => lang('High'), '0' => lang('Normal'), '-1' => lang('Low'), '-2' => lang('Lowest')),
					'categories_map'          => array('task' => $task_category_map, 'todolist' => null),
					'users_map'               => $positions_map,
					'companies_map'           => $companies_map,
					'job_types_map'           => AngieApplication::isModuleLoaded('tracking') ? JobTypes::getIdNameMap() : null,
					'add_urls'                => $add_urls,
					'subobjects_url'          => Router::assemble('project_object_template_subobjects', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--', 'template_id' => $this->active_template->getId())),
					'reorder_url'             => Router::assemble('project_object_template_reorder', array('object_id' => '--OBJECT-ID--', 'object_type' => '--OBJECT-TYPE--', 'template_id' => $this->active_template->getId())),
					'permissions'             => $permissions,
					'unclassified_label'      => $unclassified_label,
					'mass_edit_urls'          => array(
						'change_category'         => Router::assemble('project_object_template_mass_edit', array('template_id' => $this->active_template->getId(), 'mass_edit_action' => 'change_category')),
						'change_assignee'         => Router::assemble('project_object_template_mass_edit', array('template_id' => $this->active_template->getId(), 'mass_edit_action' => 'change_assignee')),
						'change_label'            => Router::assemble('project_object_template_mass_edit', array('template_id' => $this->active_template->getId(), 'mass_edit_action' => 'change_label')),
					),
					'shortcuts_url'           => Router::assemble('project_object_template_shortcuts', array('template_id' => $this->active_template->getId()))
				));

				$this->response->assign(array(
					'positions'                     => ProjectObjectTemplates::findPositionsForList($this->active_template),
					'task_categories'               => $task_categories,
					'discussion_categories'         => ProjectObjectTemplates::findCategoriesForList($this->active_template, "discussion"),
					'file_categories'               => ProjectObjectTemplates::findCategoriesForList($this->active_template, "file"),
					'files'                         => ProjectObjectTemplates::findFilesForList($this->active_template)
				));
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->notFound();
		} // if
	} // view

	/**
	 * Create new template
	 */
	function add() {
		if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
			if (ProjectTemplates::canAdd($this->logged_user)) {
				$template_data = $this->request->post('template');
				$this->response->assign('template_data', $template_data);

				if ($this->request->isSubmitted()) {
					try {
						DB::beginWork('Creating a new template @ ' . __CLASS__);

						$this->active_template = new ProjectTemplate();

						$this->active_template->setName($template_data['name']);
						$this->active_template->setAttributes($template_data);
						$this->active_template->setCompanyId($this->owner_company->getId());
						$this->active_template->setCreatedBy($this->logged_user);

						$this->active_template->save();

						DB::commit('Template created @ ' . __CLASS__);

						// get master categories
						$task_categories = ConfigOptions::getValue("task_categories");
						if (is_foreachable($task_categories)) {
							foreach ($task_categories as $name) {

								$task_category_template = new ProjectObjectTemplate('Category');

								DB::beginWork('Copy master task categories for new project template @ ' . __CLASS__);

								$task_category_template->setTemplate($this->active_template);
								$task_category_template->setValue('name', $name);
								$task_category_template->setSubtype('task');

								$task_category_template->save();

								DB::commit('Master task categories for new project template copied @ ' . __CLASS__);
							} // foreach
						} // if

						$discussion_categories = ConfigOptions::getValue("discussion_categories");
						if (is_foreachable($discussion_categories)) {
							foreach ($discussion_categories as $name) {

								$discussion_category_template = new ProjectObjectTemplate('Category');

								DB::beginWork('Copy master discussion categories for new project template @ ' . __CLASS__);

								$discussion_category_template->setTemplate($this->active_template);
								$discussion_category_template->setValue('name', $name);
								$discussion_category_template->setSubtype('discussion');

								$discussion_category_template->save();

								DB::commit('Master discussion categories for new project template copied @ ' . __CLASS__);
							} // foreach
						} // if

						$file_categories = ConfigOptions::getValue('asset_categories');
						if (is_foreachable($file_categories)) {
							foreach ($file_categories as $name) {
								$file_category_template = new ProjectObjectTemplate('Category');

								DB::beginWork('Copy master file categories for new project template @ ' . __CLASS__);

								$file_category_template->setTemplate($this->active_template);
								$file_category_template->setValue('name', $name);
								$file_category_template->setSubtype('file');

								$file_category_template->save();

								DB::commit('Master file categories for new project template copied @ ' . __CLASS__);
							} // foreach
						} // if

						if ($this->request->isPageCall()) {
							$this->response->redirectToUrl($this->active_template->getViewUrl());
						} else {
							$this->response->respondWithData($this->active_template, array(
								'as' => 'template',
								'detailed' => true,
							));
						} // if
					} catch (Exception $e) {
						DB::rollback('Failed to create a new Template @ ' . __FILE__);

						if ($this->request->isPageCall()) {
							$this->response->assign('errors', $e);
						} else {
							$this->response->exception($e);
						} // if
					} // try
				} // if
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->badRequest();
		} // if
	} // add

	function edit() {
		if ($this->active_template->isLoaded()) {
			if ($this->active_template->canEdit($this->logged_user)) {
				$template_data = $this->request->post('template', array(
					'name' => $this->active_template->getName()
				));

				$this->response->assign('template_data', $template_data);

				if ($this->request->isSubmitted()) {
					try {
						$this->active_template->setAttributes($template_data);
						$this->active_template->save();

						if ($this->request->isPageCall()) {
							$this->response->redirectToUrl($this->active_template->getViewUrl());
						} else {
							$this->response->respondWithData($this->active_template, array(
								'as' => 'template',
								'detailed' => true,
							));
						} // if
					} catch (Exception $e) {
						$this->response->exception($e);
					} // try
				} // if
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->notFound();
		} // if
	} // edit

	/**
	 * Return template positions
	 */
	function positions() {
		if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if ($this->active_template->isLoaded()) {
				$positions = $this->active_template->positions()->get();

				$this->response->respondWithData($positions, array(
					'as' => 'positions',
					'detailed' => true
				));
			} else {
				$this->response->notFound();
			}
		} else {
			$this->response->badRequest();
		}
	} // positions

	/**
	 * Return template positions
	 */
	function min_data() {
		if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if ($this->active_template->isLoaded()) {
				$total_scheduled_object = ProjectObjectTemplates::getTotalOfScheduledObjectsByTemplate($this->active_template);
				$data = array(
					'positions'     => $this->active_template->positions()->get(),
					'is_scheduled'  => $total_scheduled_object > 0 ? true : false
				);

				$this->response->respondWithData($data);
			} else {
				$this->response->notFound();
			}
		} else {
			$this->response->badRequest();
		}
	} // positions

	/**
	 * Shortcuts page
	 */
	function shortcuts() {

	} // shortcuts

	/**
	 * Delete existing template
	 */
	function delete() {
		if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if ($this->active_template->isLoaded()) {
				if ($this->active_template->canDelete($this->logged_user)) {

					$this->active_template->delete();

					$this->response->ok();
				} else {
					$this->response->forbidden();
				} // if
			} else {
				$this->response->notFound();
			} // if
		} else {
			$this->response->badRequest();
		} // if
	} // delete
}