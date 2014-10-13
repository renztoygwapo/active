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
    
    {template_vars_to_js wireframe=$wireframe}
  </head>
  <body class="phone" onorientationchange="App.Wireframe.iPhoneOrientationChange.fix();">
    <div data-role="page" data-theme="f">
    	<div data-role="header" data-theme="f">
    	{if $wireframe->breadcrumbs->hasBackPage()}
    	  <a href="{$wireframe->breadcrumbs->getBackPageUrl()}" id="wireframe_back_button" title="{$wireframe->breadcrumbs->getBackPageText()}" data-rel="back" data-icon="arrow-l">Back</a>
    	{/if}
    	{if $wireframe->actions->getPrimary() instanceof WireframeAction}
    		<style type="text/css">
	        .ui-header a[data-rel='primary'] { background: transparent url('{$wireframe->actions->getPrimary()->getIcon(AngieApplication::INTERFACE_PHONE)}') 0 0 no-repeat !important; }
	    	</style>
    	  <a href="{$wireframe->actions->getPrimary()->getUrl()}" id="{$wireframe->actions->getPrimary()->getId()}" data-rel="primary">{$wireframe->actions->getPrimary()->getText()}</a>
    	{/if}
    	<h1>
    		{if $wireframe->getPageTitle() == 'Welcome' && !ActiveCollab::getBrandingRemoved()}
    			<div class="wireframe_logo">
    				<img src="{image_url name="layout/wireframe/acLogo.png" module=$smarty.const.SYSTEM_MODULE interface=AngieApplication::INTERFACE_PHONE}" alt="" /><span>{$wireframe->getPageTitle(lang('Index'))}</span>
    			</div>
    		{else}
    			{$wireframe->getPageTitle(lang('Index'))}
    		{/if}
    	</h1>
    	</div>
    	
    	{if $wireframe->breadcrumbs->count() > 2}
    	<div data-role="breadcrumbs" class="wireframe_breadcrumbs">
    	  <ul>
    	  {foreach $wireframe->breadcrumbs as $breadcrumb_name => $breadcrumb}
    	    <li id="breadcrumb_{$breadcrumb_name}">
    	    {if $breadcrumb.url}
    	    	<a href="{$breadcrumb.url}">{if $breadcrumb_name != 'home'}{$breadcrumb.text}{/if}</a>
    	    {else}
    	      {$breadcrumb.text}
    	    {/if}
    	    </li>
    	  {/foreach}
    	  </ul>
    	</div>
    	{/if}
    
    	<div data-role="content" class="wireframe_content">{$content_for_layout nofilter}</div>
    
      {if $wireframe->actions->count() > 0}
      <style type="text/css">
    	{foreach $wireframe->actions as $wireframe_action}
        #{$wireframe_action->getId()} .ui-icon { background: url('{$wireframe_action->getIcon(AngieApplication::INTERFACE_PHONE)}') 50% 50% no-repeat; }
      {/foreach}
    	</style>
      
      <div data-role="footer" data-position="fixed" class="wireframe_actions" data-theme="g">
      	 <div data-role="navbar">
      		<ul>
      		{foreach $wireframe->actions as $wireframe_action}
            <li><a href="{$wireframe_action->getUrl()}" id="{$wireframe_action->getId()}" data-icon="custom"></a></li>
          {/foreach}
      		</ul>
      	</div>
      </div>
      {/if}
    </div>
    
    <script type="text/javascript">
    	$(document).ready(function() {
  			App.Wireframe.MobileBreadCrumbs.init('wireframe_breadcrumbs', {$wireframe->breadcrumbs|json nofilter});
  			App.Wireframe.ConfirmationDialogs.init();

        $('.wireframe_logo').parent().addClass('show_logo');
  		});
  		
  		// prevents links from full-screen apps from opening in mobile safari
  		(function(document,navigator,standalone) {
				if((standalone in navigator) && navigator[standalone]) {
					var curnode, location=document.location, stop=/^(a|html)$/i;
					document.addEventListener('click', function(e) {
						curnode=e.target;
						while(!(stop).test(curnode.nodeName)) {
							curnode=curnode.parentNode;
						} // while
						
						var attr = $(curnode).attr('data-rel');
						
						var is_back_button = false;
						if(typeof attr !== 'undefined' && attr !== false && attr == 'back') {
							is_back_button = true;
						} // if
						
						var is_select_box = false;
						if(typeof curnode.href !== 'undefined' && curnode.href.substr(-1) == "#") {
							is_select_box = true;
						} // if
						
						// Conditions to do this only on links to your own app. If you want all links, use if('href' in curnode) instead.
						if(
							!is_back_button && 																						 // back button is not affected
							!is_select_box && 																						 // select box isn't affected, too
							'href' in curnode && 																					 // is a link
							(chref=curnode.href).replace(location.href,'').indexOf('#') && // is not an anchor
							(!(/^[a-z\+\.\-]+:/i).test(chref) ||                       		 // either does not have a proper scheme (relative links)
								chref.indexOf(location.protocol+'//'+location.host)===0) 		 // or is in the same protocol and domain
						) {
							e.preventDefault();
							location.href = curnode.href;
						} else if(is_back_button && !is_select_box) {
							window.location.href = curnode.href;
						} // if
					}, false);
				} // if
			})(document, window.navigator, 'standalone');
		</script>
  </body>
</html>