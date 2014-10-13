<?php

  /**
   * Initalize visual editor framework
   *
   * @package angie.frameworks.visual_editor
   */

  define('VISUAL_EDITOR_FRAMEWORK', 'visual_editor');
  define('VISUAL_EDITOR_FRAMEWORK_PATH', ANGIE_PATH . '/frameworks/visual_editor');
  
  define('VISUAL_EDITOR_FRAMEWORK_INJECT_INTO', 'system');
	
  AngieApplication::setForAutoload(array(
    'ICodeSnippets' => VISUAL_EDITOR_FRAMEWORK_PATH . '/models/ICodeSnippets.class.php',
    'ICodeSnippetsImplementation' => VISUAL_EDITOR_FRAMEWORK_PATH . '/models/ICodeSnippetsImplementation.class.php',

    'FwCodeSnippet' => VISUAL_EDITOR_FRAMEWORK_PATH . '/models/code_snippets/FwCodeSnippet.class.php',
    'FwCodeSnippets' => VISUAL_EDITOR_FRAMEWORK_PATH . '/models/code_snippets/FwCodeSnippets.class.php',
  ));