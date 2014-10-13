<!DOCTYPE html> 
<html> 
	<head>
		{if AngieApplication::isInDevelopment() || AngieApplication::isInDebugMode()}
	    <meta http-equiv="cache-control" content="no-cache">
			<meta http-equiv="pragma" content="no-cache">
  	{/if}
		
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
  	<title>{$wireframe->getPageTitle(lang('Index'))}</title>
  	
  	<link rel="shortcut icon" href="{brand what=favicon}" type="image/x-icon" />
  	
  	<link rel="apple-touch-icon-precomposed" sizes="144x144" href="{touch what='icon' size='144x144'}" />
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="{touch what='icon' size='114x114'}" />
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="{touch what='icon' size='72x72'}" />
		<link rel="apple-touch-icon-precomposed" href="{touch what='icon'}" />
  	
  	<link rel="apple-touch-startup-image" href="{touch what='startup'}">
  	
  	<meta name="viewport" content="width=640, initial-scale=0.5, target-densitydpi=device-dpi" />
  	<meta name="apple-mobile-web-app-capable" content="yes" />
  	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
  	
  	<link rel="stylesheet" href="{$wireframe->getCollectedStylesheetsUrl(AngieApplication::INTERFACE_PHONE, AngieApplication::CLIENT_IPHONE)}" type="text/css" media="screen" id="style_main_css"/>
    <script type="text/javascript" src="{$wireframe->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_PHONE, AngieApplication::CLIENT_IPHONE)}"></script>
  </head>
  <body class="phone backend_login" onorientationchange="App.Wireframe.iPhoneOrientationChange.fix();">
    <div data-role="page" data-theme="f">
    	<div id="login_splash">
    		<img src="{touch what='login-logo'}" alt="{lang}Logo{/lang}">
    	</div>
    	
    	<div data-role="content" class="wireframe_content top_shadow">{$content_for_layout nofilter}</div>
    </div>
  </body>
</html>

<script type="text/javascript">
	// prevents links from full-screen apps from opening in mobile safari
	(function(document,navigator,standalone) {
		if((standalone in navigator) && navigator[standalone]) {
			var curnode, location=document.location, stop=/^(a|html)$/i;
			document.addEventListener('click', function(e) {
				curnode=e.target;
				while(!(stop).test(curnode.nodeName)) {
					curnode=curnode.parentNode;
				} // while
				
				// Conditions to do this only on links to your own app. If you want all links, use if('href' in curnode) instead.
				if(
					'href' in curnode && 																					 // is a link
					(chref=curnode.href).replace(location.href,'').indexOf('#') && // is not an anchor
					(!(/^[a-z\+\.\-]+:/i).test(chref) ||                       		 // either does not have a proper scheme (relative links)
						chref.indexOf(location.protocol+'//'+location.host)===0) 		 // or is in the same protocol and domain
				) {
					e.preventDefault();
					location.href = curnode.href;
				} // if
			}, false);
		} // if
	})(document, window.navigator, 'standalone');
</script>