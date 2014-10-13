<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$locale_code}" lang="{$locale_code}" class="loading">
<head>
  <!-- Meta -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" >
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

{if activeCollab::getBrandingRemoved()}
  <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/not-branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
{else}
  <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
{/if}
  <meta name="msapplication-TileColor" content="#95000">

  <title>{lang}Loading{/lang}...</title>
	<link rel="shortcut icon" href="{brand what=favicon}" type="image/x-icon" />

  <!--[if IE]>
  <script language="javascript" type="text/javascript" src="{$wireframe->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), 'ie', true)}"></script>
  <![endif]-->
  <!--[if lte IE 9]>
    <script language="javascript" type="text/javascript" src="{$wireframe->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), 'ie9', true)}"></script>
  <![endif]-->
  
  <!-- Head tags -->
  {if $wireframe->getAllHeadTags()}
    {foreach $wireframe->getAllHeadTags() as $head_tag}
      {$head_tag nofilter}
    {/foreach}
  {/if}

  {if is_foreachable($wireframe->getRssFeeds())}
    {foreach $wireframe->getRssFeeds() as $rss_feed}
      <link rel="alternate" type="{$rss_feed.feed_type}" title="{$rss_feed.title}" href="{$rss_feed.url}" />
    {/foreach}
  {/if}

  <style type="text/css">
    html.loading {
      margin: 0px;
      padding: 0px;
      height: 100%;
      font-family: Verdana,​ Arial,​ Helvetica,​ sans-serif;
    }

    html.loading body {
      padding: 0px;
      margin: 0px;
      height: 100%;
      background: #ecede4;
      position: relative;
    }

    html.loading body div#page_preloader {
      width: 300px;
      height: 56px;
      text-align: center;
      position: absolute;
      left: 50%;
      top: 50%;
      margin-left: -150px;
      margin-top: -20px;
    }

    html.loading body div#page_preloader h3 {
      font-size: 12px;
      margin: 0px 0px 13px 0px;
      color: #68695E;
      text-shadow: 0px 1px 0px #FFF;
      font-weight: bold !important;
    }

    html.loading body div#page_preloader div#page_preloader_progressbar {
      background: #FFFFFF;
      height: 28px;
      border-radius: 10px;
      position: relative;
      overflow: hidden;
      background: url('{$smarty.const.ASSETS_URL nofilter}/images/environment/default/layout/login/loader.gif') no-repeat center center;
    }

    #error_page {
      margin: 0px;
      padding: 0px;
      font: 12px "Lucida Grande", Verdana, Arial, Helvetica, sans-serif;
      color: #333333;
      text-align: center;
    }

    #error_box {
      width: 680px;
      margin: 0px auto 25px;
      border: 1px solid #ccc;
      padding: 30px 30px;
      text-align: left;
      background: #FFF;
      border-radius: 15px;
      box-shadow: 0px 0px 5px #ccc;
    }

    #error_box h1 {
      margin: 0px;
      padding: 0px 0px 0px 20px;
      font-size: 16px;
      color: #950000;
      background: url(assets/images/environment/default/layout/bits/indicator-warning.png) no-repeat left 2px;
      font-weight: bold;
    }

    #error_box p {
      line-height: 160%;
      margin: 15px 0px 5px;
      color: #666;
    }

    #error_box .description {
      border-top: 1px solid #ddd;
      margin-top: 5px;
    }

    #error_page a {
      color: #950000;
    }

    #error_page #company_logo {
      margin-top: 20px;
      margin-bottom: 15px;
    }

    #error_page img {
      border: 0px;
    }

    #supported_browsers_message {
      list-style: none;
      margin: 20px 0px 0px 0px;
      padding: 0px;
      overflow: hidden;
      zoom: 1;
    }

    #supported_browsers_message li {
      width: 20%;
      float: left;
      text-align: center;
    }

    #supported_browsers_message li a {
      display: block;
      background: #f8f8f8;
      margin: 0px 4px;
      padding: 10px 0px;
      border-radius: 10px;
      text-decoration: none !important;
    }

    #supported_browsers_message li a:hover {
      background: #f3f3dc;
      text-decoration: none !important;
    }

    #supported_browsers_message li span.browser_name {
      display: block;
      margin-top: 5px;
      font-weight: bold;
    }

    #supported_browsers_message li span.browser_version {
      display: block;
      margin-top: 5px;
      color: #666;
      font-size: 11px;
    }
  </style>

  {if !($wireframe->getInitParams($logged_user) instanceof User)}
      <!-- *%NOT LOGGED IN%* -->
  {/if}

</head>

<body class="{implode values=$wireframe->getBodyClasses() separator=' '}">

  <div id="page_preloader">
    <h3>{lang}Loading{/lang} ...</h3>
    <div id="page_preloader_progressbar"></div>
  </div>

  <script type="text/javascript">
      // initial variables
      var initial_variables = {template_vars_to_js wireframe=$wireframe wrap=false};

      var stylesheets_loaded = false;
      var javascript_loaded = false;
      var page_initialized = false;
      var document_head = document.head ? document.head : document.getElementsByTagName('head')[0];

      var initialize_page = function () {
        if (stylesheets_loaded && javascript_loaded && !page_initialized) {
          // remove preloader
          var preloader = document.getElementById('page_preloader');
          preloader.parentNode.removeChild(preloader);

          // remove class from html element
          document.documentElement.removeAttribute('class');

          // set template vars
          App.Config.reset(initial_variables);

          App.Wireframe.Navigation.init({
            'init_params'       : {$wireframe->getInitParams($logged_user)|json nofilter},
            'loaded_content'    : {$content_for_layout|json nofilter},
            'loaded_url'        : {$request->getUrl()|json nofilter}
          });
        } // if
      }; // initialize_page

      var load_style_sheet = function ( path, fn, scope, id ) {
        var head = document.getElementsByTagName( 'head' )[0], // reference to document.head for appending/ removing link nodes
        link = document.createElement( 'link' );           // create the link node
        link.setAttribute( 'href', path );
        link.setAttribute( 'rel', 'stylesheet' );
        link.setAttribute( 'type', 'text/css' );
        link.setAttribute( 'id', id );

        if (navigator && navigator.userAgent.indexOf('MSIE 8.') != -1) {
          var stylesheet_interval = setInterval(function () {
            for (var x in document.styleSheets) {
              if (document.styleSheets[x]['id'] == id) {
                clearInterval(stylesheet_interval);
                stylesheets_loaded = true;
                initialize_page();
              } // if
            } // for
          }, 50);
        } else {
          var sheet, cssRules;
          if ( 'sheet' in link ) {
            sheet = 'sheet'; cssRules = 'cssRules';
          } else {
            sheet = 'styleSheet'; cssRules = 'rules';
          } // if

          var interval_id = setInterval( function() {                    // start checking whether the style sheet has successfully loaded
            try {
              if ( link[sheet] && link[sheet][cssRules].length ) { // SUCCESS! our style sheet has loaded
                clearInterval( interval_id );                     // clear the counters
                clearTimeout( timeout_id );
                fn.call( scope || window, true, link );           // fire the callback with success == true
              }
            } catch( e ) {} finally {}
          }, 10 ),                                                   // how often to check if the stylesheet is loaded
          timeout_id = setTimeout( function() {       // start counting down till fail
            clearInterval( interval_id );            // clear the counters
            clearTimeout( timeout_id );
            head.removeChild( link );                // since the style sheet didn't load, remove the link node from the DOM
            fn.call( scope || window, false, link ); // fire the callback with success == false
          }, 15000 );
        } // if

        document_head.appendChild( link );
        return link;
      } // load_style_sheet

      var load_script = function (url, callback, id) {
        // create script element
        var script_tag = document.createElement('script');
        script_tag.type = "text/javascript";
        script_tag.src = url;
        script_tag.id = id;

        if (navigator && navigator.userAgent.indexOf('MSIE 8.') != -1) {
          var javascript_interval = setInterval(function () {
            if (window.main_javascript_loaded) {
              callback();
              clearInterval(javascript_interval);
            } // if
          }, 50);

        } else {
          script_tag.onload = callback;
        } // if

        document_head.appendChild(script_tag);
        return script_tag
      } // load_script

      /**
       * Get list of supported browsers
       */
      var get_supported_browsers_list = function () {
        var supported_browsers_list = '<ul id="supported_browsers_message">';
        supported_browsers_list += '<li><a href="http://www.google.com/chrome" target="_blank"><img src="' + initial_variables['assets_url'] + '/images/environment/default/supported-browsers/google-chrome.png' + '"><span class="browser_name">Google Chrome</span><span class="browser_version">Latest Available</span></a></li>';
        supported_browsers_list += '<li><a href="http://www.mozilla.com/firefox" target="_blank"><img src="' + initial_variables['assets_url'] + '/images/environment/default/supported-browsers/mozilla-firefox.png' + '"><span class="browser_name">Mozilla Firefox</span><span class="browser_version">Latest Available</span></a></li>';
        supported_browsers_list += '<li><a href="http://www.apple.com/safari" target="_blank"><img src="' + initial_variables['assets_url'] + '/images/environment/default/supported-browsers/apple-safari.png' + '"><span class="browser_name">Apple Safari</span><span class="browser_version">Latest Available</span></a></li>';
        supported_browsers_list += '<li><a href="http://www.microsoft.com/windows/internet-explorer" target="_blank"><img src="' + initial_variables['assets_url'] + '/images/environment/default/supported-browsers/internet-explorer.png' + '"><span class="browser_name">Internet Explorer</span><span class="browser_version">Version 9+</span></a></li>';
        supported_browsers_list += '<li><a href="http://www.google.com/chromeframe" target="_blank"><img src="' + initial_variables['assets_url'] + '/images/environment/default/supported-browsers/chrome-frame.png' + '"><span class="browser_name">Chrome Frame</span><span class="browser_version">For IE 6,7</span></a></li>';
        supported_browsers_list += '</ul>';
        return supported_browsers_list;
      }; // get_supported_browsers_list

      // default message
      var unsupported_browser_message = false;
      // get the user agent
      var user_agent = navigator.userAgent;

      {literal}
      // browser is internet explorer
      if (user_agent && user_agent.indexOf('MSIE') != -1) {

        // get the internet explorer version
        var regular_expression  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
        var internet_explorer_version = false;
        var rounded_internet_explorer_version = false;
        if (regular_expression.exec(user_agent) != null) {
          internet_explorer_version = parseFloat( RegExp.$1 );
          rounded_internet_explorer_version = parseInt(internet_explorer_version);
        } // if

        if (internet_explorer_version) {
          // using IE10 in compatibility mode || using IE9 in compatibility mode || using IE8 in compatibility mode
          if (
            ((user_agent.indexOf('Trident/6.0') != -1) && (rounded_internet_explorer_version != 10)) ||
            ((user_agent.indexOf('Trident/5.0') != -1) && (rounded_internet_explorer_version != 9))
          ) {
            unsupported_browser_message = '<p><strong>You are running Internet Explorer in Compatibility mode. This mode makes it run as Internet Explorer 7, which is not supported.</strong></p>';
            unsupported_browser_message+= '<p>Please disable Compatibility mode or use one of the supported browsers:</p>';
            unsupported_browser_message+= get_supported_browsers_list();

          // runing obsoletete internet explorer version
          } else if (internet_explorer_version < 9.0) {
            unsupported_browser_message = '<p><strong>You are using an unsupported version of Internet Explorer!</strong></p>';
            unsupported_browser_message+= '<p>Please upgrade your browser to the latest version, or use one of the supported browsers:</p>';
            unsupported_browser_message+= get_supported_browsers_list();
          } // if
        } // if

      } // if
      {/literal}

      // if we are using unsupported browser
      if (unsupported_browser_message) {
        // update the browser title
        document.getElementsByTagName('title')[0].text = 'Browser Compatibility Notice';

        // remove preloader
        var preloader = document.getElementById('page_preloader');
        preloader.parentNode.removeChild(preloader);

        // remove class from html element
        document.documentElement.removeAttribute('class');

        // get the body element
        var body = document.getElementsByTagName('body')[0];

        // set the ID of body element
        body.setAttribute('id', 'error_page');
        body.setAttribute('class', 'wide');

        // display the branding logo
        var company_logo = document.createElement('div');
        company_logo.setAttribute('id', 'company_logo');
        company_logo.innerHTML = '<a href="' + initial_variables['url_base'] + '"><img alt="" src="' + initial_variables['branding_url'] + 'logo.80x80.png"></a>';
        body.appendChild(company_logo);

        // display the error box
        var error_box = document.createElement('div');
        error_box.setAttribute('id', 'error_box');
        error_box.innerHTML = '<h1>Browser Compatibility Notice</h1><div class="description">' + unsupported_browser_message + '</div>';
        body.appendChild(error_box);
      } else {
        // load the page
        load_script('{$wireframe->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), $wireframe->getAssetsContext()) nofilter}', function () {
          javascript_loaded = true; initialize_page();
        }, 'main_javascript');

        load_style_sheet('{$wireframe->getCollectedStylesheetsUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), $wireframe->getAssetsContext()) nofilter}', function () {
          stylesheets_loaded = true;
          initialize_page();
        }, null, 'main_stylesheet');
      } // if

  </script>
</body>
</html>