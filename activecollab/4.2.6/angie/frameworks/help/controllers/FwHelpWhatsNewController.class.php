<?php

  // Build on top of the application level controller
  AngieApplication::useController('help', HELP_FRAMEWORK_INJECT_INTO);

  /**
   * Framework level what's new controller
   *
   * @package angie.frameworks.help
   * @subpackage controllers
   */
  abstract class FwHelpWhatsNewController extends HelpController {

    /**
     * Execute before any other action
     */
    function __before() {
      parent::__before();

//      $this->wireframe->breadcrumbs->add('whats_new', lang("What's New?"), Router::assemble('help_whats_new'));
      $this->wireframe->tabs->setCurrentTab('whats_new');

      $articles = AngieApplication::help()->getWhatsNew($this->logged_user);

      $articles_by_version = array();

      foreach($articles as $article) {
        $version = $article->getVersionNumber();

        if(empty($articles_by_version[$version])) {
          $articles_by_version[$version] = array($article);
        } else {
          $articles_by_version[$version][] = $article;
        }
      } // foreach

      $this->response->assign(array(
        'articles' => $articles,
        'articles_by_version' => $articles_by_version,
      ));

      $this->setView('index');
    } // __before

    /**
     * Show what's new
     */
    function index() {

    } // index

    /**
     * Show a single article
     */
    function article() {
      $this->response->assign('selected_article', $this->request->get('article_name'));
    } // article

  }