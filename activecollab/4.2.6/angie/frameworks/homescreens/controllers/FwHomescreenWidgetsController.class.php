<?php

// Build on top of administration controller
AngieApplication::useController('backend', HOMESCREENS_FRAMEWORK_INJECT_INTO);

/**
 * Framework level home screen controller implementation
 *
 * @package angie.frameworks.homescreens
 * @subpackage controllers
 */
class FwHomescreenWidgetsController extends BackendController {

  /**
   * Active home screen widget
   *
   * @var HomescreenWidget
   */
  protected $active_homescreen_widget;

  /**
   * Execute before any action is executed
   */
  function __before() {
    parent::__before();

    $homescreen_widget_id = $this->request->getId('widget_id');
    if($homescreen_widget_id) {
      $this->active_homescreen_widget = HomescreenWidgets::findById($homescreen_widget_id);
    } // if
  } // __before

  /**
   * Renders the homescreen widget
   */
  function render() {
    if (!$this->request->isAsyncCall()) {
      $this->response->badRequest();
    } // if

    if(!($this->active_homescreen_widget instanceof HomescreenWidget && $this->active_homescreen_widget->isLoaded())) {
      $this->response->notFound();
    } // if

    $temp_widget_id = $this->request->get('custom_widget_id', HTML::uniqueId('homescreen_widget'));

    $this->response->respondWithData(array(
      'title'   => $this->active_homescreen_widget->renderTitle($this->logged_user, $temp_widget_id),
      'body'    => $this->active_homescreen_widget->renderBody($this->logged_user, $temp_widget_id),
      'footer'  => $this->active_homescreen_widget->renderFooter($this->logged_user, $temp_widget_id)
    ));
  } // renders the homescreen widget
}