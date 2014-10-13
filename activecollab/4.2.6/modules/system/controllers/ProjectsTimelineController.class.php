<?php

AngieApplication::useController('projects', SYSTEM_MODULE);

/**
 * Main ProjectsTimeline controller
 *
 * @package activeCollab.modules.system
 * @subpackage controllers
 */
class ProjectsTimelineController extends ProjectsController {

	/**
	 * Construct ProjectsTimeline controller
	 *
	 * @param Request $parent
	 * @param null $context
	 */
	public function __construct(Request $parent, $context = null) {
		parent::__construct($parent, $context);
	} // __construct

	/**
	 * Prepare Controller
	 */
	public function __before() {
		parent::__before();

		if (($this->request->isWebBrowser() || $this->request->isMobileDevice()) && in_array($this->request->getAction(), array('index', 'view'))) {
			$this->wireframe->tabs->setCurrentTab('projects_timeline');
			$this->wireframe->breadcrumbs->add('projects_timeline', lang('Timeline'), Router::assemble('projects_timeline'));

			if(Projects::canAdd($this->logged_user)) {
				$flyout_options = false;
				if ($this->request->getAction() == 'view') {
					$flyout_options = array('success_message' => lang('Project has been created successfully'));
				} // if

				$this->wireframe->actions->add('new_project', lang('New Project'), Router::assemble('projects_add'), array(
					'onclick' => new FlyoutFormCallback('project_created', $flyout_options),
					'icon' => AngieApplication::getImageUrl('layout/button-add.png', ENVIRONMENT_FRAMEWORK, AngieApplication::getPreferedInterface()),
					'primary' => true
				));
			} // if
		} // if
	} // __before

	/**
	 * Display main ProjectsTimeline page
	 */
	public function index() {
		AngieApplication::useHelper('datetime', GLOBALIZATION_FRAMEWORK, 'modifier');

		if ($this->request->isPrintCall()) {
			$this->smarty->assign(array(
				'page_title'  => lang('Projects'),
				'projects'    => Projects::findForTimeline($this->logged_user)
			));
		} elseif ($this->request->isWebBrowser()) {
			AngieApplication::useWidget('projects_timeline_diagram', SYSTEM_MODULE);
			$this->wireframe->print->enable();

			$this->wireframe->actions->add('archive', lang('Archive'), Router::assemble('projects_archive'));

			$day_width = 17;
			$this->smarty->assign(array(
				'page_title' => lang('Projects'),
				'projects' => Projects::findForTimeline($this->logged_user),
				'day_width'  => $day_width,
				'diagram_images' => array(
					'days' => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'days', 'day_width' => $day_width, 'work_days' => Globalization::getWorkdays())),
					'week_days'  => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'week_days', 'day_width' => $day_width, 'day_names' => Globalization::getShortDayNames())),
					'month_days'  => AngieApplication::getProxyUrl("milestone_timeline_images", SYSTEM_MODULE, array('type' => 'month_days', 'day_width' => $day_width))
				)
			));
		} // if
	} // index

}