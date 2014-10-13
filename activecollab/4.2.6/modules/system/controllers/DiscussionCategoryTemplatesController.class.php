<?php

/**
 *
 */
class DiscussionCategoryTemplatesController extends ProjectObjectTemplatesController {

	/**
	 * Active discussion category template
	 *
	 * @var DiscussionCategoryTemplate
	 */
	protected $active_discussion_category_template;

	/**
	 * Construct discussion category template request
	 *
	 * @param Request $parent
	 * @param null $context
	 */
	function __construct(Request $parent, $context = null) {
		parent::__construct($parent, $context);
	} // __construct

	/**
	 * Prepare controller
	 */
	function __before() {
		parent::__before();
		if (DiscussionCategoryTemplates::canManage($this->logged_user)) {
			$discussion_category_template_id = $this->request->get("task_category_template_id");
			if ($discussion_category_template_id) {
				$this->active_discussion_category_template = DiscussionCategoryTemplates::findById($discussion_category_template_id);
			}

			if (!($this->active_discussion_category_template instanceof DiscussionCategoryTemplate)) {
				$this->active_discussion_category_template = new DiscussionCategoryTemplate();
				$this->active_discussion_category_template->setTemplate($this->active_template);
			}
		} else {
			$this->response->forbidden();
		}
	} // __before

	/**
	 * Add new task category
	 */
	function add() {
		if ($this->request->isAsyncCall() || ($this->request->isApiCall() && $this->request->isSubmitted()) || $this->request->isMobileDevice()) {
			if (DiscussionCategoryTemplates::canAdd($this->logged_user)) {

				$discussion_category_template_data = $this->request->get("discussion_category_template");

				if ($this->request->isSubmitted()) {
					try {
						DB::beginWork("Creating new discussion category template @ " . __CLASS__);

						$this->active_discussion_category_template->setAttributes($discussion_category_template_data);
						$this->active_discussion_category_template->setCreatedBy($this->logged_user);

						$this->active_discussion_category_template->save();

						DB::commit("Discussion category template created @ " . __CLASS__);

						if ($this->request->isPageCall()) {
							$this->flash->success('Discussion category template ":name" has been created', array('name' => $this->active_discussion_category_template->getName()));
							$this->response->redirectToUrl($this->active_discussion_category_template->getViewUrl());
						} else {
							$this->response->respondWithData($this->active_discussion_category_template, array(
								'as' => 'discussion_category_template',
								'detailed' => true,
							));
						} // if
					} catch (Exception $e) {
						DB::rollback('Failed to create discussion category template @ ' . __CLASS__);
						$this->response->exception($e);
					} // try
				} // if
			} else {
				$this->response->forbidden();
			} // if
		} else {
			$this->response->badRequest();
		} // if
	} // add

	/**
	 * Edit task category
	 */
	function edit() {

	} // edit

	/**
	 * Delete task category
	 */
	function delete() {

	} // delete

}
