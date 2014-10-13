<?php

  /**
   * Twitter popup callback
   *
   * @package angie.frameworks.help
   * @subpackage models
   */
  class TwitterIntentCallback extends JavaScriptCallback {

    // Intent types
    const INTENT_TWEET = 'tweet';
    const INTENT_RETWEET = 'retweet';
    const INTENT_FAVORITE = 'favorite';
    const INTENT_USER_PROFILE = 'user';

    /**
     * Intent
     *
     * @var $message
     */
    private $intent;

    /**
     * Parameters
     *
     * @var array
     */
    private $params;

    /**
     * Construct twitter popup callback
     *
     * @param string $intent
     * @param array|null $params
     */
    function __construct($intent = TwitterIntentCallback::INTENT_TWEET, $params = null) {
      $this->intent = $intent;
      $this->params = $params;
    } // __construct

    /**
     * Render callback
     *
     * @return string
     */
    function render() {
      $url = 'https://twitter.com/intent/' . $this->intent;

      if($this->params) {
        $url .= '?' . http_build_query($this->params);
      } // if

      $height = $this->intent === TwitterIntentCallback::INTENT_USER_PROFILE ? 520 : 420;

      return '(function() { $(this).click(function() { console.log(this); window.open("' . $url . '", "intent", "scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=550,height=' . $height . ',top=50,left=100"); return false; }); })';
    } // render

  }