<?php

// Build on top of admin controller
AngieApplication::useController('admin', ENVIRONMENT_FRAMEWORK_INJECT_INTO);

/**
 * Appearance profile controller
 *
 * @package angie.frameworks.environment
 * @subpackage controllers
 */
class FwAppearanceController extends AdminController {

  /**
   * Active color scheme id
   *
   * @var null
   */
  protected $active_color_scheme_id = null;

  /**
   * Active color scheme
   *
   * @var array
   */
  protected $active_color_scheme = null;

  /**
   * before
   */
  function __before() {
    parent::__before();

    $this->active_color_scheme_id = $this->request->get('scheme_id');
    if ($this->active_color_scheme_id) {
      $this->active_color_scheme = ColorSchemes::get($this->active_color_scheme_id);
      if (!$this->active_color_scheme) {
        $this->response->notFound();
      } // if
    } else {
      $this->active_color_scheme = array();
    } // if
  } // before

  /**
   * Index action
   */
  function index() {
    $widget_options = array(
      'schemes' => ColorSchemes::getAll(),
      'default_scheme' => ColorSchemes::get('default'),
      'add_scheme_url' => Router::assemble('appearance_admin_add_scheme'),
      'edit_scheme_url' => Router::assemble('appearance_admin_edit_scheme', array('scheme_id' => '--SCHEME-ID--')),
      'rename_scheme_url' => Router::assemble('appearance_admin_rename_scheme', array('scheme_id' => '--SCHEME-ID--')),
      'delete_scheme_url' => Router::assemble('appearance_admin_delete_scheme', array('scheme_id' => '--SCHEME-ID--')),
      'set_as_default_scheme_url' => Router::assemble('appearance_admin_set_as_default_scheme', array('scheme_id' => '--SCHEME-ID--')),
      'active_scheme_id' => ColorSchemes::getCurrentSchemeId()
    );

    $this->response->assign('widget_options', $widget_options);
  } // index

  /**
   * Add new Scheme
   */
  function add() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      $this->active_color_scheme_id = $this->request->post('id');
      $this->active_color_scheme = array(
        'name' => $this->request->post('name'),
        'background_color' => $this->request->post('background_color'),
        'outer_color' => $this->request->post('outer_color'),
        'inner_color' => $this->request->post('inner_color'),
        'link_color' => $this->request->post('link_color')
      );

      ColorSchemes::add($this->active_color_scheme_id, $this->active_color_scheme);

      $this->response->respondWithData(ColorSchemes::get($this->active_color_scheme_id));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // if
  } // add

  /**
   * Edit existing scheme
   */
  function edit() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      $this->active_color_scheme = array(
        'name' => $this->request->post('name'),
        'background_color' => $this->request->post('background_color'),
        'outer_color' => $this->request->post('outer_color'),
        'inner_color' => $this->request->post('inner_color'),
        'link_color' => $this->request->post('link_color')
      );

      ColorSchemes::update($this->active_color_scheme_id, $this->active_color_scheme);

      $this->response->respondWithData(ColorSchemes::get($this->active_color_scheme_id));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // if
  } // edit

  /**
   * Rename existing scheme
   */
  function rename() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      $new_id = $this->request->post('new_id');
      $new_name = $this->request->post('new_name');
      if (!$new_name || !$new_id) {
        $this->response->badRequest();
      } // if

      ColorSchemes::rename($this->active_color_scheme_id, $new_id, $new_name);

      $this->active_color_scheme_id = $new_id;

      $this->response->respondWithData(ColorSchemes::get($this->active_color_scheme_id));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // if
  } // rename

  /**
   * Delete existing scheme
   */
  function delete() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      ColorSchemes::delete($this->active_color_scheme_id);

      $this->response->respondWithData(ColorSchemes::get($this->active_color_scheme_id));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // if
  } // delete

  /**
   * Set as default
   */
  function set_as_default() {
    if (!$this->request->isSubmitted()) {
      $this->response->badRequest();
    } // if

    try {
      $this->active_color_scheme = array(
        'name' => $this->request->post('name'),
        'background_color' => $this->request->post('background_color'),
        'outer_color' => $this->request->post('outer_color'),
        'inner_color' => $this->request->post('inner_color'),
        'link_color' => $this->request->post('link_color')
      );

      if (!ColorSchemes::isBuiltIn($this->active_color_scheme_id)) {
        ColorSchemes::update($this->active_color_scheme_id, $this->active_color_scheme); // update color scheme
      } // if

      ColorSchemes::setCurrentSchemeId($this->active_color_scheme_id); // set this scheme as current

      $this->response->respondWithData(ColorSchemes::get($this->active_color_scheme_id));
    } catch (Exception $e) {
      $this->response->exception($e);
    } // if
  } // set_as_default

} // FwAppearanceController