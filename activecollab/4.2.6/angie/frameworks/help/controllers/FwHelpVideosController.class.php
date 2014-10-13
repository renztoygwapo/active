<?php

  // Build on top of the application level controller
  AngieApplication::useController('help', HELP_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level help videos controller
   *
   * @package angie.frameworks.help
   * @subpackage controllers
   */
  abstract class FwHelpVideosController extends HelpController {

    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();

      $this->wireframe->tabs->setCurrentTab('videos');

      $this->response->assign(array(
        'video_groups' => AngieApplication::help()->getVideoGroups(),
        'videos' => AngieApplication::help()->getVideos($this->logged_user),
        'player_url' => AngieApplication::getAssetUrl('jwplayer/player.swf', ENVIRONMENT_FRAMEWORK, 'flash'),
      ));

      $this->setView('index');
    } // __before

    /**
     * Show index page
     */
    function index() {

    } // index

    /**
     * Select a single video
     */
    function video() {
      $this->response->assign('selected_video', $this->request->get('video_name'));
    } // video

  }