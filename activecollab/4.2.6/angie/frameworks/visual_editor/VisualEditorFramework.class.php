<?php

  /**
   * Visual editor framework definition
   *
   * @package angie.frameworks.visual_editor
   */
  class VisualEditorFramework extends AngieFramework {
    
    /**
     * Framework name
     *
     * @var string
     */
    protected $name = 'visual_editor';
    
        /**
     * Define environment framework updates
     */
    function defineRoutes() {
			Router::map('code_snippets_add', 'code-snippets/add', array('controller' => 'code_snippets', 'action' => 'add', 'module' => VISUAL_EDITOR_FRAMEWORK_INJECT_INTO));
			
			Router::map('code_snippet', 'code-snippets/:code_snippet_id', array('controller' => 'code_snippets', 'action' => 'view', 'module' => VISUAL_EDITOR_FRAMEWORK_INJECT_INTO), array('code_snippet_id' => Router::MATCH_ID));
			Router::map('code_snippet_edit', 'code-snippets/:code_snippet_id/edit', array('controller' => 'code_snippets', 'action' => 'edit', 'module' => VISUAL_EDITOR_FRAMEWORK_INJECT_INTO), array('code_snippet_id' => Router::MATCH_ID));
			Router::map('code_snippet_delete', 'code-snippets/:code_snippet_id/delete', array('controller' => 'code_snippets', 'action' => 'delete', 'module' => VISUAL_EDITOR_FRAMEWORK_INJECT_INTO), array('code_snippet_id' => Router::MATCH_ID));

      Router::map('code_snippet_preview', 'code-snippets/preview', array('controller' => 'code_snippets', 'action' => 'preview', 'module' => VISUAL_EDITOR_FRAMEWORK_INJECT_INTO));
    } // defineRoutes
    
    /**
     * Define event handlers
     */
    function defineHandlers() {
      EventsManager::listen('on_rawtext_to_richtext', 'on_rawtext_to_richtext');
    } // defineHandlers
    
  }