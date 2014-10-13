<?php

  /**
   * Initialize LiveChat session JavaScript callback
   *
   * @package angie.frameworks.help
   * @subpackage models
   */
  class InitLiveChatCallback extends JavaScriptCallback {

    /**
     * Render callback code
     *
     * @return string
     */
    function render() {
      return '(function() { $(this).targetBlank(); })';
    } // render

  }