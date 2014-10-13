<?php

  /**
   * Definition of code snippets interface
   *
   * @package angie.framework.visual_editor
   * @subpackage models
   */
  interface ICodeSnippets {
    
    /**
     * Return code snippets implementation
     *
     * @return ICodeSnippetsImplementation
     */
    public function code_snippets();
    
  }