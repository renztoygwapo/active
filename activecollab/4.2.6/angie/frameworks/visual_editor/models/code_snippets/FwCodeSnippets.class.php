<?php

  /**
   * CodeSnippets class
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  abstract class FwCodeSnippets extends BaseCodeSnippets {
  	
    /**
     * Returns true if $user can add code snippets
     *
     * @param User $user
     * @return boolean
     */
    static function canAdd(User $user) {
      return true;
    } // canAdd
  }