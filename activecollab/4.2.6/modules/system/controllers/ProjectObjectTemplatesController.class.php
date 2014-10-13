<?php

AngieApplication::useController('project_templates', SYSTEM_MODULE);

/**
 * Main project template objects request controller
 *
 * @package activeCollab.modules.system
 * @subpackage controllers
 */
class ProjectObjectTemplatesController extends ProjectTemplatesController {

	/**
	 * Active object request
	 *
	 * @var ProjectObjectTemplate
	 */
	protected $active_object;

	/**
	 * Construct project template object requests controller
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
		if (ProjectObjectTemplates::canManage($this->logged_user)) {

			$object_type = ucfirst(strtolower($this->request->get("object_type")));

			// This is only for edit object
			$object_id = $this->request->get("object_id");
			if ($object_id) {
				$this->active_object = ProjectObjectTemplates::findById($object_id);
			} // if

			if (!($this->active_object instanceof ProjectObjectTemplate)) {
				if ($object_type == "Template") {
					$this->active_object = new ProjectObjectTemplate("Milestone");
				} else {
					$this->active_object = new ProjectObjectTemplate($object_type);
				}
				$this->active_object->setTemplate($this->active_template);
			} // if

			$this->smarty->assign(array(
				'logged_user'   => $this->logged_user,
				'active_object' => $this->active_object
			));
		} else {
			$this->response->forbidden();
		} // if
	} // __before

	/**
	 * Add new object
	 */
	function add() {
		if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if (ProjectObjectTemplates::canAdd($this->logged_user)) {

				$object_data = $this->request->post(strtolower($this->active_object->getType()));
				$specify = (integer) array_var($object_data, 'specify', 0);
				if (!$specify) {
					unset($object_data['start_on'], $object_data['due_on']);
				} // if
				$parent_id = $this->request->get("parent_id");

				if ($this->request->isSubmitted()) {
					try {
						DB::beginWork('Creating ' . $this->active_object->getType() . ' @ ' . __CLASS__);

						$this->active_object->setValues($object_data);
						$this->active_object->setParentId($parent_id);

						switch($this->active_object->getType()) {
							case "Category":
								$this->active_object->setSubtype($this->request->get("category_type"));
								break;
						} // switch

						$this->active_object->save();

						DB::commit($this->active_object->getType() . ' created @ ' . __CLASS__);

						if ($this->request->isPageCall()) {
							$this->flash->success($this->active_object->getType() . ' ":name" has been created', array('name' => $this->active_object->getName()));
							$this->response->redirectToUrl($this->active_object->getViewUrl());
						} else {
							$this->response->respondWithData($this->active_object, array(
								'as' => strtolower($this->active_object->getType()),
								'detailed' => true,
							));
						} // if
					} catch (Exception $e) {
						DB::rollback('Failed to create ' . $this->active_object->getType() . ' @ ' . __CLASS__);

						if ($this->request->isPageCall()) {
							$this->smarty->assign('errors', $e);
						} else {
							$this->response->exception($e);
						} // if
					} // try
				} // if

				$uploader_options = array(
					'upload_url'          => Router::assemble('project_file_template_upload_compatibility', array('template_id' => $this->active_template->getId(), 'object_type' => 'file')),
					'edit_button_url'     => AngieApplication::getAssetUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
					'delete_button_url'   => AngieApplication::getAssetUrl('icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK),
					'default_file_icon'   => get_file_icon_url("", "48x48"),
					'size_limit'          => get_max_upload_size(),
					'files'               => ProjectObjectTemplates::findFilesForList($this->active_template)
				);

				$uploader_options = array_merge($uploader_options, array(
					'uploader_runtimes'           => FILE_UPLOADER_RUNTIMES,
					'flash_uploader_url'          => AngieApplication::getAssetUrl('plupload.flash.swf', FILE_UPLOADER_FRAMEWORK, 'flash'),
					'silverlight_uploader_url'    => AngieApplication::getAssetUrl('plupload.silverlight.xap', FILE_UPLOADER_FRAMEWORK, 'silverlight'),
					'upload_name'                 => 'file'
				));

				$this->response->assign(array(
					'object_data'       => $object_data,
					'add_object_url'    => Router::assemble('project_object_template_add', array("template_id" => $this->active_template->getId(), 'object_type' => strtolower($this->active_object->getType()))),
					'form_id'           => HTML::uniqueId('form'),
					'uploader_options'  => $uploader_options
				));

				$this->setView(strtolower($this->active_object->getType())."_add");
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->badRequest();
		} // if
	} // add

	/**
	 * File add
	 */
	function files_add() {
		if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if (ProjectObjectTemplates::canAdd($this->logged_user)) {
				$file_data = $this->request->post('file');

				if ($this->request->isSubmitted()) {
					// associate files with attachments
					try {
						DB::beginWork('Upload of multiple files started @ ' . __CLASS__);

						$attachments = $this->request->post('attachments');
						if (!is_foreachable($attachments)) {
							throw new Exception(lang('No files uploaded'));
						} // if

						$descriptions =$this->request->post('descriptions');

						$result = array();
						foreach ($attachments as $index => $attachment_id) {
							$attachment = Attachments::findById($attachment_id);

							if (!($attachment instanceof Attachment)) {
								throw new Exception(lang('Could not find attachment with id :attachment_id', array('attachment_id' => $attachment_id)));
							} // if

							$data_to_set = array(
								'name'        => $attachment->getName(),
								'category_id' => array_var($file_data, 'category_id'),
								'body'        => array_var($descriptions, $index),
								'location'    => $attachment->getLocation(),
								'mime_type'   => $attachment->getMimeType(),
								'state'       => STATE_VISIBLE,
								'version'     => 1
							);

							$file_template = new ProjectObjectTemplate('File');
							$file_template->setTemplate($this->active_template);
							$file_template->setValues($data_to_set);
							$file_template->setFileSize($attachment->getSize());

							$file_template->save();

							$result[] = array(
								'name'        => $file_template->getValue('name'),
								'icon'        => get_file_icon_url($file_template->getValue('name'), "48x48"),
								'file_size'   => $file_template->getFileSize() ? format_file_size($file_template->getFileSize()) : 0,
								'permissions' => array(
									'can_delete'  => $file_template->canDelete($this->logged_user)
								),
								'urls'        => array(
									'delete'      => Router::assemble('project_object_template_delete', array('template_id' => $this->active_template->getId(), 'object_type' => 'file', 'object_id' => $file_template->getId()))
								)
							);
						} // foreach

						DB::execute("DELETE FROM " . TABLE_PREFIX . "attachments WHERE id IN (?)", $attachments);

						DB::commit('Multiple file upload succeeded @ ' . __CLASS__);

						$this->response->respondWithData($result, array(
							'as' => 'files',
							'detailed' => true,
						));
					} catch (Exception $e) {
						DB::rollback('Upload of multiple files failed @ ' . __CLASS__);
						$this->response->exception($e);
					} // try
				} // if
			} else {
				$this->response->forbidden();
			} // if

			$uploader_options = array(
				'upload_url'          => Router::assemble('project_file_template_upload_compatibility', array('template_id' => $this->active_template->getId(), 'object_type' => 'file')),
				'edit_button_url'     => AngieApplication::getAssetUrl('icons/12x12/edit.png', ENVIRONMENT_FRAMEWORK),
				'delete_button_url'   => AngieApplication::getAssetUrl('icons/12x12/delete.png', ENVIRONMENT_FRAMEWORK),
				'default_file_icon'   => get_file_icon_url("", "48x48"),
				'size_limit'          => get_max_upload_size(),
				'files'               => ProjectObjectTemplates::findFilesForList($this->active_template)
			);

			$uploader_options = array_merge($uploader_options, array(
				'uploader_runtimes'           => FILE_UPLOADER_RUNTIMES,
				'flash_uploader_url'          => AngieApplication::getAssetUrl('plupload.flash.swf', FILE_UPLOADER_FRAMEWORK, 'flash'),
				'silverlight_uploader_url'    => AngieApplication::getAssetUrl('plupload.silverlight.xap', FILE_UPLOADER_FRAMEWORK, 'silverlight'),
				'upload_name'                 => 'file'
			));

			$this->response->assign(array(
				'object_data'       => $file_data,
				'upload_url'        => Router::assemble('project_template_file_add', array("template_id" => $this->active_template->getId())),
				'form_id'           => HTML::uniqueId('form'),
				'uploader_options'  => $uploader_options
			));
		} else {
			$this->response->badRequest();
		} // if
	} // file_add

	/**
	 * Uploads the file in compatibility mode
	 */
	function upload_compatibility() {
		$advanced_upload = $this->request->get('advanced_upload');

		if($this->request->isSubmitted()) {
			if(ProjectObjectTemplates::canAdd($this->logged_user, $this->active_project)) {
				$this->smarty->assign(array(
					'form_id' => $this->request->get('form_id'),
					'row_index' => $this->request->get('row_index')
				));

				try {
					DB::beginWork('Creating attachment');

					$uploaded_file = array_var($_FILES, 'file', null);

					if ($uploaded_file['error']) {
						throw new Error(get_upload_error_message($uploaded_file['error']));
					} // if

					if (!$uploaded_file) {
						throw new Error(lang('File not uploaded correctly'));
					} // if

					if (FwDiskSpace::isUsageLimitReached() || !FwDiskSpace::has($uploaded_file['size'])) {
						throw new Error(lang('Disk Quota Reached. Please consult your system administrator.'));
					} // if

					$new_name = AngieApplication::getAvailableUploadsFileName();
					if (!move_uploaded_file($uploaded_file['tmp_name'], $new_name)) {
						throw new Error(lang('Could not move uploaded file to uploads folder. Check folder permissions'));
					} // if

					$attachment = new Attachment();
					$attachment->setName(array_var($uploaded_file, 'name'));
					$attachment->setSize(filesize($new_name));
					$attachment->setLocation(basename($new_name));
					$attachment->setMimeType(get_mime_type($new_name, $attachment->getName()));
					$attachment->setCreatedBy($this->logged_user);
					$attachment->setCreatedOn(new DateTimeValue());
					$attachment->save();

					DB::commit('Attachment created');

					if ($advanced_upload) {
						$this->response->setContentType(BaseHttpResponse::PLAIN);
						echo JSON::encode($attachment, $this->logged_user, true, true);
						die();
					} else {
						$this->smarty->assign('attachment_id', $attachment->getId());
					} // if
				} catch (Exception $e) {
					DB::commit('Failed to create attachment');

					if ($new_name && is_file($new_name)) {
						@unlink($new_name);
					} // if

					if ($advanced_upload) {
						$this->response->respondWithData($e);
					} else {
						$this->smarty->assign('error_message', $e->getMessage());
					} // if
				} // try
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->badRequest();
		} // if
	} // upload_compatibility

	/**
	 * Edit object
	 */
	function edit() {
		if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if ($this->active_object->isLoaded()) {
				if ($this->active_object->canEdit($this->logged_user)) {
					$object_data = $this->request->post(strtolower($this->active_object->getType()));

					if (!is_array($object_data)) {
						$object_data = $this->active_object->getValues();
					} else {
						$specify = (integer) array_var($object_data, 'specify', 0);
						if (!$specify) {
							unset($object_data['start_on'], $object_data['due_on']);
						} // if
					} // if

					if ($this->request->isSubmitted()) {
						try {
							DB::beginWork('Updating ' . $this->active_object->getType() . ' @ ' . __CLASS__);

							$this->active_object->setValues($object_data);
							$this->active_object->save();

							DB::commit($this->active_object->getType() . ' updated @ ' . __CLASS__);

							if ($this->request->isPageCall()) {
								$this->flash->success($this->active_object->getType() . ' ":name" has been created', array('name' => $this->active_object->getName()));
								$this->response->redirectToUrl($this->active_object->getViewUrl());
							} else {
								$this->response->respondWithData($this->active_object, array(
									'as' => strtolower($this->active_object->getType()),
									'detailed' => true,
								));
							} // if
						} catch (Exception $e) {
							DB::rollback('Failed to update ' . $this->active_object->getType() . ' @ ' . __CLASS__);

							if ($this->request->isPageCall()) {
								$this->smarty->assign('errors', $e);
							} else {
								$this->response->exception($e);
							} // if
						} // try
					} // if

					$this->response->assign(array(
						'edit_object_url' => Router::assemble('project_object_template_edit', array("template_id" => $this->active_template->getId(), 'object_type' => strtolower($this->active_object->getType()), 'object_id' => $this->active_object->getId())),
						'object_data' => $object_data
					));

					$this->setView(strtolower($this->active_object->getType())."_edit");
				} else {
					$this->response->forbidden();
				}
			} else {
				$this->response->notFound();
			}
		} else {
			$this->response->badRequest();
		}
	} // edit

	/**
	 * Delete object
	 */
	function delete() {
		if ($this->request->isAsyncCall() || $this->request->isMobileDevice() || ($this->request->isApiCall() && $this->request->isSubmitted())) {
			if ($this->active_object->isLoaded()) {
				if ($this->active_object->canDelete($this->logged_user)) {

					// delete active project object template and return null
					$this->active_object->delete();

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

	/**
	 * Reorder items
	 */
	function reorder() {
		// check if request is submitted and async
		if (!($this->request->isSubmitted() && $this->request->isAsyncCall())) {
			$this->response->badRequest();
		} // if

		$object_type = strtolower($this->request->get('object_type', null));
		$object_id = $this->request->getId('object_id', null);

		$active_object = null;
		$result = array();

		if ($object_type == 'milestone') {
			$parent_id = null;

			// check if we have permission to manage tasks
			$task_ids = $this->request->post('task');
			if (is_foreachable($task_ids)) {
				if (!ProjectObjectTemplates::canManage($this->logged_user)) {
					$this->response->forbidden();
				} // if
			} // if

			// try to find the parent object (in case of milestone, the parent object can be unknown milestone which doesn not exists in database)
			$active_object = ProjectObjectTemplates::findById($object_id);
			if ($active_object instanceof ProjectObjectTemplate && !$active_object->isNew()) {
				$parent_id = $active_object->getId();
			} else {
				$parent_id = 0;
			} // if

			try {
				DB::beginWork('Project template outline start sorting');

				// update tasks positions, and parent attributes
				if (is_foreachable($task_ids)) {
					$counter = 0;
					foreach ($task_ids as $task_id) {
						DB::execute('UPDATE `' . TABLE_PREFIX . 'project_object_templates` SET `position` = ?, parent_id = ? WHERE `id` = ? AND `template_id` = ? AND `type` = ?', $counter, $parent_id, $task_id, $this->active_template->getId(), 'Task');
						$counter++;
					} // foreach

					$updated_tasks = ProjectObjectTemplates::findByIds($task_ids);
					$result = array_merge((array)$result, (array)$updated_tasks->toArray());
				} // if

				DB::commit('Project template outline sorting successful');
			} catch (Exception $e) {
				db::rollback('Project template outline sorting failed');
				$this->response->exception($e);
			} // if
		} else if ($object_type == 'task') {

			$active_object = ProjectObjectTemplates::findById($object_id);

			// check if object exists
			if (!$active_object || $active_object->isNew()) {
				$this->response->notFound();
			} // if

			// check if we can reoreder subtasks
			if (!$active_object->canEdit($this->logged_user)) {
				$this->response->forbidden();
			} // if

			try {
				$subtask_ids = $this->request->post('subtask');
				if (is_foreachable($subtask_ids)) {
					$counter = 0;
					foreach ($subtask_ids as $subtask_id) {
						DB::execute('UPDATE `' . TABLE_PREFIX . 'project_object_templates` SET `position` = ?, parent_id = ? WHERE `id` = ?', $counter, $active_object->getId(), $subtask_id);
						$counter++;
					} // foreach

					$result = ProjectObjectTemplates::findByIds($subtask_ids);
				} // if
			} catch (Exception $e) {
				$this->response->exception($e);
			} // try
		} else {
			// object type is not supported, return bad request
			$this->response->badRequest();
		} // if

		$this->response->respondWithData($result, array('detailed' => true));
	} // reorder

	/**
	 * Mass edit action
	 */
	function mass_edit() {
		if (!$this->request->isSubmitted()) {
			$this->response->badRequest();
		} // if

		$object_ids = $this->request->post('selected_items');

		$updated_objects = array();
		switch ($this->request->get('mass_edit_action')) {

			// change category logic
			case 'change_category':
				// only tasks can have categories
				$category_id = $this->request->post('category_id') ? $this->request->post('category_id') : null;

				if (is_foreachable($object_ids['task'])) {
					try {
						DB::beginWork('project template outline mass edit change category started');

						/**
						 * @var ProjectObjectTemplate $task
						 */
						$tasks = ProjectObjectTemplates::findByIds($object_ids['task']);
						if (is_foreachable($tasks)) {
							foreach ($tasks as $task) {
								if ($task->canEdit($this->logged_user)) {
									$task->setValue("category_id", $category_id);
									$task->save();
									$updated_objects[] = $task;
								} // if
							} // if
						} // if

						DB::commit('project template outline mass edit change category successful');
					} catch (Exception $e) {
						DB::rollback('project template outline mass edit change category failed');
						$this->response->exception($e);
					} // try
				} // if
				break;

			// change label
			case 'change_label':
				$label_id = $this->request->post('label_id') ? $this->request->post('label_id') : null;

				if (is_foreachable($object_ids['task']) || is_foreachable($object_ids['subtask'])) {
					try {
						DB::beginWork('project template outline mass edit change label started');

						/**
						 * @var ProjectObjectTemplate $task
						 */
						if (is_foreachable($object_ids['task'])) {
							$tasks = ProjectObjectTemplates::findByIds($object_ids['task']);
							if (is_foreachable($tasks)) {
								foreach ($tasks as $task) {
									if ($task->canEdit($this->logged_user)) {
										$task->setValue("label_id", $label_id);
										$task->save();
										$updated_objects[] = $task;
									} // if
								} // if
							} // if
						} // if

						/**
						 * @var ProjectObjectTemplate $subtask
						 */
						if (is_foreachable($object_ids['subtask'])) {
							$subtasks = ProjectObjectTemplates::findByIds($object_ids['subtask']);
							if (is_foreachable($subtasks)) {
								foreach ($subtasks as $subtask) {
									if ($subtask->canEdit($this->logged_user)) {
										$subtask->setValue("label_id", $label_id);
										$subtask->save();
										$updated_objects[] = $subtask;
									} // if
								}
								;
							} // if
						} // if

						DB::commit('project template outline mass edit change label successful');
					} catch (Exception $e) {
						DB::rollback('project template outline mass edit change label failed');
						$this->response->exception($e);
					} // try
				} // if
				break;

			// change assignee
			case 'change_assignee':
				$assignee_id = $this->request->post('assignee_id') ? $this->request->post('assignee_id') : null;
				if (is_foreachable($object_ids['task']) || is_foreachable($object_ids['subtask']) || is_foreachable($object_ids['milestone'])) {
					try {
						DB::beginWork('project template outline mass edit change assignee started');

						/**
						 * @var ProjectObjectTemplate $milestone
						 */
						// update milestones
						if (is_foreachable($object_ids['milestone'])) {
							$milestones = ProjectObjectTemplates::findByIds($object_ids['milestone']);
							if (is_foreachable($milestones)) {
								foreach ($milestones as $milestone) {
									if ($milestone->canEdit($this->logged_user)) {
										$milestone->setValue("assignee_id", $assignee_id);
										$milestone->save();
										$updated_objects[] = $milestone;
									} // if
								} // foreach
							} // if
						} // if

						/**
						 * @var ProjectObjectTemplate $task
						 */
						// update tasks
						if (is_foreachable($object_ids['task'])) {
							$tasks = ProjectObjectTemplates::findByIds($object_ids['task']);
							if (is_foreachable($tasks)) {
								foreach ($tasks as $task) {
									if ($task->canEdit($this->logged_user)) {
										$task->setValue("assignee_id", $assignee_id);
										$task->save();
										$updated_objects[] = $task;
									} // if
								} // if
							} // if
						} // if

						/**
						 * @var ProjectObjectTemplate $subtask
						 */
						if (is_foreachable($object_ids['subtask'])) {
							$subtasks = ProjectObjectTemplates::findByIds($object_ids['subtask']);
							if (is_foreachable($subtasks)) {
								foreach ($subtasks as $subtask) {
									if ($subtask->canEdit($this->logged_user)) {
										$subtask->setValue("assignee_id", $assignee_id);
										$subtask->save();
										$updated_objects[] = $subtask;
									} // if
								}
								;
							} // if
						} // if

						DB::commit('project template outline mass edit change assignee successful');
					} catch (Exception $e) {
						DB::rollback('project template outline mass edit change assignee failed');
						$this->response->exception($e);
					} // try
				} // if
				break;

			default:
				$this->response->badRequest();
				break;
		} // switch

		$this->response->respondWithData($updated_objects, array('detailed' => true));
	} // mass_edit

	/**
	 * Renders subobjects for some object
	 */
	function subobjects() {
		if (!$this->request->isAsyncCall()) {
			$this->response->badRequest();
		} // if

		$object_type = ucfirst(strtolower($this->request->get('object_type')));

		$subobjects = array();

		if ($object_type == "Template") {
			$this->response->respondWithData(ProjectObjectTemplates::findObjectsForOutline($this->active_template), array('detailed' => true));
		} else {
			$this->response->respondWithData(ProjectObjectTemplates::findObjectsForOutline($this->active_template, $this->active_object), array('detailed' => true));
		}

		$this->response->respondWithData($subobjects, array('detailed' => true));
	} // subobjects

}
