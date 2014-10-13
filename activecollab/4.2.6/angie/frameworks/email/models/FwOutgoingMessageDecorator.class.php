<?php

  /**
   * Abstract outgoing message decorator implementation
   * 
   * Appliation mailer adapter class with sole purpose of preparing message 
   * bodies before they are being sent to the user
   * 
   * @package angie.frameworks.mailer
   * @subpackage models
   */
  abstract class FwOutgoingMessageDecorator {

    /**
     * Construct decorator
     */
    function __construct() {
      ColorSchemes::initializeForCompile();
    } // __construct
  	
  	/**
  	 * Wrap input message body and prepare it to be sent
  	 * 
  	 * In case of list of messages, this function will prepare digest body and 
  	 * wrap it with header and footer. In case of single message, it will simply 
  	 * use the only body and wrap it with header and footer
  	 * 
  	 * $decorate value is ignored for digests - they are always decorated, even 
  	 * when $decorate is set to false
  	 * 
  	 * @param mixed $messages
  	 * @param boolean $decorate
  	 * @return array
     * @throws InvalidInstanceError
  	 */
    function wrap($messages, $decorate = true) {
  		if(is_foreachable($messages) && count($messages) > 1) {
  			if(count($messages) > 1) {
  				return array($this->getDigestSubject($messages), $this->wrapDigest(first($messages)->getRecipient(), $messages));  				
  			} else {
  				return array($this->getSingleSubject($messages[0]), $this->wrapSingle($messages[0], $decorate));
  			} // if
  		} elseif($messages instanceof OutgoingMessage) {
  			return array($this->getSingleSubject($messages), $this->wrapSingle($messages, $decorate));
  		} else {
  			throw new InvalidInstanceError('messages', $messages, array('OutgoingMessage', 'array'));
  		} // if
  	} // wrap
  	
  	/**
  	 * Wrap single message
  	 * 
  	 * @param OutgoingMessage $message
  	 * @param boolean $decorate
  	 * @return string
  	 */
  	protected function wrapSingle(OutgoingMessage $message, $decorate = true) {
  	  if($decorate) {
        $result = $this->renderHeader($message->getRecipient(), $message->getParent(), false);
    	  $result.= $message->getBody();
        $result.= $this->renderFooter($message->getRecipient(), $message->getParent(), $message->getUnsubscribeUrl(), false);
    	  return $result;
  	  } else {
  	    return $message->getBody();
  	  } // if
  	} // wrapSingle
  	
  	/**
  	 * Return single message subject
  	 * 
  	 * @param OutgoingMessage $message
  	 * @return string
  	 */
  	function getSingleSubject(OutgoingMessage $message) {
  	  return $message->getContextId() ? $message->getSubject() . ' {' . $message->getContextId() . '}' : $message->getSubject();
  	} // getSingleSubject
  	
  	/**
  	 * Return subject for digest message
  	 * 
  	 * @param string $messages
  	 * @return string
  	 */
  	function getDigestSubject($messages) {
  		return lang('Messages');
  	} // getDigestSubject
  	
  	/**
  	 * Wrap multiple messages
  	 * 
  	 * @param IUser $recipient
  	 * @param array $messages
     * @return string
  	 */
    protected function wrapDigest(IUser $recipient, $messages) {
  		$result = $this->renderHeader($recipient, null, true);

  		// Digest navigation
  		$result .= $this->openSection();
  		$result .= '<p>' . lang('This is a single email that contains following messages', null, true, $recipient->getLanguage()) . ':</p>';
  		
  		$result .= '<ol>';
  		foreach($messages as $message) {
  			$result .= '<li><a href="outgoing-message-' . $message->getId() . '">' . clean($message->getSubject()) . '</a></li>';
  		} // foreach
  		$result .= '</ol>';
  		
  		$result .= '<p>' . lang('Please scroll through the entire message to see all individual messages', null, true, $recipient->getLanguage()) . '.</p>';
  		$result .= $this->closeSection();
  		
  		// Now lets render all individual messages
  		foreach($messages as $message) {
  			$result .= $this->openSection(array(
  			  'id' => 'outgoing-message-' . $message->getId(), 
  			));
  			
  			$result .= $this->renderSectionHeader($message->getSubject());
  			$result .= $message->getBody();
  			
  			if($message->getParent() instanceof ISubscriptions && $message->getParent()->subscriptions()->isSubscribed($recipient)) {
  				$result .= '<p>' . lang('<a href=":unsubscribe_url">Click here</a> to stop receiveing notifications about this :type', array(
  				  'unsubscribe_url' => $message->getParent()->subscriptions()->getUnsubscribeUrl($recipient), 
  				  'type' => $message->getParent()->getVerboseType(true, $recipient->getLanguage()), 
  				), true, $recipient->getLanguage()) . '.</p>';
  			} // if
  			
  			$result .= $this->closeSection();
  		} // foreach
  		
  		return $result . $this->renderFooter($recipient, false, false, true);
  	} // wrapDigest

    // ---------------------------------------------------
    //  Theme and Styling
    // ---------------------------------------------------

    /**
     * Return email notification wrapper background color
     *
     * @return string
     */
    function getBackgroundColor() {
      return ColorSchemes::getContextPopupBackgroundColor();
    } // getBackgroundColor

    /**
     * Return font family for the email notification
     *
     * @return string
     */
    function getFontFamily() {
      return 'Lucida Grande, Verdana, Arial, Helvetica, sans-serif';
    } // getFontFamily

    /**
     * Return link style
     *
     * @return string
     */
    function getLinkStyle() {
      return 'color: ' . ColorSchemes::getLinkColor() . '; text-decoration: underline;';
    } // getLinkStyle

    /**
     * Return reply above this line color
     *
     * @return string
     */
    function getReplyAboveThisLineColor() {
      return ColorSchemes::getContextPopupTitleTextColor();
    } // getReplyAboveThisLineColor

    /**
     * Get Reply above this line bacground color
     *
     * @return string
     */
    function getReplyAboveThisLineBackgroundColor() {
      return ColorSchemes::getContextPopupTitleBackgroundColor3();
    } // getReplyAboveThisLineBackgroundColor

    /**
     * Return reply above this line border color
     *
     * @return string
     */
    function getReplyAboveThisLineBorderColor() {
      return ColorSchemes::getContextPopupTitleBackgroundColor2();
    } // getReplyAboveThisLineBorderColor
  	
  	// ---------------------------------------------------
  	//  Renderers
  	// ---------------------------------------------------
  	
  	/**
  	 * Render message header
  	 *
  	 * @param IUser $recipient
  	 * @param mixed $context
  	 * @param boolean $digest
  	 * @return string
  	 */
  	abstract protected function renderHeader(IUser $recipient, $context = null, $digest = false);

  	/**
  	 * Render message footer
  	 *
  	 * @param IUser $recipient
     * @param mixed $context
     * @param mixed $unsubscribe_url
  	 * @param boolean $digest
  	 * @return string
  	 */
    abstract protected function renderFooter(IUser $recipient, $context = null, $unsubscribe_url = false ,$digest = false);
  	
  	// ---------------------------------------------------
  	//  Sections
  	// ---------------------------------------------------
  	
  	/**
  	 * Open section counter
  	 * 
  	 * @var integer
  	 */
  	private $section_counter = 0;
  	
  	/**
  	 * Flag that determines whether we have an open section, or not
  	 * 
  	 * @var boolean
  	 */
  	private $section_opened = false;
  	
  	/**
  	 * Open a new notification section
  	 * 
  	 * @param array $attributes
  	 * @return string
  	 */
  	protected function openSection($attributes = null) {
  		$result = '';
  		
  		if($this->section_opened) {
  			$result .= $this->closeSection();
  		} // if
  		
  		$this->section_counter++;
  		$this->section_opened = true;
  		
  		return $result . open_html_tag('div', $attributes);
  	} // openSection
  	
  	/**
  	 * Slose section
  	 * 
  	 * @return string
  	 */
  	protected function closeSection() {
  		if($this->section_opened) {
  			$this->section_opened = false;
  		  return '</div>';
  		} else {
  			return '';
  		} // if
  	} // closeSection
  	
  	/**
  	 * Render section header
  	 * 
  	 * @param string $text
  	 * @return string
  	 */
  	protected function renderSectionHeader($text) {
  		return '<h2>' . $text . '</h2>';
  	} // renderSectionHeader
  	
  }