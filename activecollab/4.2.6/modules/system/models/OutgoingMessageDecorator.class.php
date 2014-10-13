<?php

  /**
   * Outgoing message decorator
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class OutgoingMessageDecorator extends FwOutgoingMessageDecorator {

    /**
     * Render message header
     *
     * @param IUser $recipient
     * @param mixed $context
     * @param boolean $digest
     * @return string
     */
    protected function renderHeader(IUser $recipient, $context = null, $digest = false) {
      $return = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
      $return .= '<html xmlns="http://www.w3.org/1999/xhtml">';
      $return .= '<head>';
      $return .= 	'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
      $return .= 	'<meta name="viewport" content="width=device-width, initial-scale=1.0" />';
      $return .= 	'<title>' . lang('Email Notification', null, true, $recipient->getLanguage()) . '</title>';
      $return .= 	'<style type="text/css">

										#outlook a {padding:0;}

										body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;}

										.ExternalClass {width:100%;}

										.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}

										#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}

										img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}

										a img {border:none;}

										.image_fix {display:block;}

										p {margin: 1em 0; line-height:14px !important;}

										h1, h2, h3, h4, h5, h6 {color: black !important;}

										h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

										h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active { color: red !important;}

										h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited { color: purple !important; }

										table td {border-collapse: collapse;}

										table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }


										@media only screen and (max-device-width: 480px) {
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											a[href^="tel"], a[href^="sms"] { text-decoration: none; color: black; pointer-events: none; cursor: default; }
											.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] { text-decoration: default; color: orange !important; pointer-events: auto; cursor: default; }
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
											#mainContent{ padding: 20px 5px !important;}
											td[id=mainContent]{	padding: 20px 5px; }
											td[class=avatar] img{ width: 30px !important; height: 30px !important;}
											td[class=avatar]{ padding-left:5px !important; padding-right:5px !important; width:30px;}
											td[class=branding] img{ width: 40px; height: 40px;}
											td[class=branding]{ padding-left:5px !important; padding-right:5px !important;}
											table[id=inspector]{ padding-left:5px !important; padding-right:5px !important; width: auto !important;}
											table[id=createdBy]{ width:100px !important;}
											td[id=content]{padding: 10px !important;}
											td[id=content] td{padding: 5px !important;}
											td[id=content] img{max-width:270px;}
										}

										@media only screen and (min-device-width: 481px) and (max-device-width: 768px) {
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											a[href^="tel"], a[href^="sms"] { text-decoration: none; color: blue; pointer-events: none; cursor: default;}
											.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] { text-decoration: default; color: orange !important; pointer-events: auto; cursor: default;}
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
										}

										@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
											table[id=attachment]{ width:auto !important; }
											a[href^="tel"], a[href^="sms"] { text-decoration: none; color: blue; pointer-events: none; cursor: default;}
											.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] { text-decoration: default; color: orange !important; pointer-events: auto; cursor: default; }
											table[id=inspector]{ width: auto !important;}
											#mainTable, #mainContent{ width: auto !important; max-width: 656px !important; }
										}

										@media only screen and (-webkit-min-device-pixel-ratio: 2) {
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											table[id=inspector]{ width: auto !important;}
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
										}

										@media only screen and (-webkit-device-pixel-ratio:.75){
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											table[id=inspector]{ width: auto !important;}
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
										}

										@media only screen and (max-device-width: 480px) and (-webkit-device-pixel-ratio:1) {
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											table[id=inspector]{ width: auto !important;}
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
											td[id=mainContent]{ padding: 20px 5px !important;}
											td[class=avatar] img{ width: 30px !important; height: 30px !important;}
											td[class=avatar]{padding-left:5px !important; padding-right:5px !important; width:30px;}
											td[class=branding] img{ width: 40px; height: 40px;}
											td[class=branding]{padding-left:5px !important; padding-right:5px !important;}
											table[id=inspector]{ padding-left:5px !important; padding-right:5px !important;}
											table[id=createdBy]{ width:100px !important;}
										}

										@media only screen and (-webkit-device-pixel-ratio:1.5){
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											table[id=inspector]{ width: auto !important;}
											table[id=mainTable], td[id=mainContent]{ width: auto !important; max-width: 656px !important;}
										}
									</style>

									<!--[if IEMobile]>
										<style type="text/css">
											#inspector{ width: auto !important;}
											#mainTable, #mainContent{ width: auto !important;  max-width:300px !important;}
											table[id=greetings], table[id=attachment]{ width:auto !important; }
											td[id=mainContent]{ padding: 20px 5px !important;}
											td[class=avatar] img{ width: 20px; height: 20px;}
											td[class=avatar]{padding-left:5px !important; padding-right:5px !important; width:20px;}
											td[class=branding] img{ width: 40px; height: 40px;}
											td[class=branding]{padding-left:5px !important; padding-right:5px !important;}
											table[id=inspector]{ padding-left:5px !important; padding-right:5px !important;}
											table[id=createdBy]{ width:100px !important;}
										</style>
									<![endif]-->

									<!--[if gte mso 9]>
									<style>
										td[id=openInBrowser]{ padding:0 !important; font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; }
										td[id=openInBrowser] a{ padding:5px 10px !important; font-family: Lucida Grande, Verdana, Arial, Helvetica, sans-serif; }
									</style>
									<![endif]-->';
      $return .= '</head>';
      $return .= '<body>';
      $return .=		'<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable" width="100%" style="font-family: ' . $this->getFontFamily() . '; background: ' . $this->getBackgroundColor() . ';" align="center">';

      if($context instanceof IComments) {
        $return .=			'<tr><td v-align="top" align="center" style="text-transform: uppercase; font-size: 11px; height:20px; background: ' . $this->getReplyAboveThisLineBackgroundColor() . '; color: ' . $this->getReplyAboveThisLineColor() . '; text-align: center; v-align:top; padding: 5px 0;">' . lang(EMAIL_SPLITTER, null, null, $recipient->getLanguage()) . '</td></tr>';
        $return .=			'<tr><td style="border-bottom:1px solid ' . $this->getReplyAboveThisLineBorderColor() . '; height:1px; line-height:1; padding:0; margin:0;"></td></tr>';
      } // if

      $return .= 		'<tr><td style="padding:40px 20px;" cellpadding="0" cellspacing="0" id="mainContent" align="center">';

      return $return;
    } // renderHeader

    /**
     * Render message footer
     *
     * @param IUser $recipient
     * @param mixed $context
     * @param mixed $unsubscribe_url
     * @param boolean $digest
     * @return string
     */
    protected function renderFooter(IUser $recipient, $context = null, $unsubscribe_url = false ,$digest = false) {
      $footer = '</td></tr>';

      $language = $recipient->getLanguage();

      if($unsubscribe_url) {
        if($context instanceof ApplicationObject) {
          $unsubscribe_message = lang('<a href=":unsubscribe_url" style=":link_style">Stop receiving email notifications</a> about this :object_type.', array(
            'unsubscribe_url' => $unsubscribe_url,
            'link_style' => $this->getLinkStyle(),
            'object_type' => $context->getVerboseType(true, $language),
          ), true, $language);
        } else {
          $unsubscribe_message = '<a href="' . clean($unsubscribe_url) . '" style="' . $this->getLinkStyle() . '">' . lang('Stop receiving these notifications', null, true, $language) . '</a>';
        } // if

        $footer.= '<tr><td style="text-align: center; padding: 5px 0; font-size: 11px;">' . $unsubscribe_message . '</td></tr>';
      } // if

      $footer.= '<tr><td style="text-align: center; padding: 5px 0 20px 0; font-size: 11px;">&copy;' . date('Y') . ' by ' . clean(Companies::findOwnerCompany()->getName());

      if(!AngieApplication::getAdapter()->getBrandingRemoved()) {
        $footer.= '. ' . lang('Powered by', null, true, $language) . ': <a href="' . clean(AngieApplication::getUrl()) . '" target="_blank" style="' . $this->getLinkStyle() . '">' . clean(AngieApplication::getName()) . '</a>.';
      } // if

      return "$footer</td></tr></table></body></html>";
    } // renderFooter

  }