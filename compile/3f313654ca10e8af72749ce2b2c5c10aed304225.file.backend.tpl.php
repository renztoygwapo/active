<?php /* Smarty version Smarty-3.1.12, created on 2014-10-03 05:46:08
         compiled from "C:\wamp\www\dev\activecollab\4.2.6\modules\system\layouts\backend.tpl" */ ?>
<?php /*%%SmartyHeaderCode:4200542e382040a6f3-30983907%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3f313654ca10e8af72749ce2b2c5c10aed304225' => 
    array (
      0 => 'C:\\wamp\\www\\dev\\activecollab\\4.2.6\\modules\\system\\layouts\\backend.tpl',
      1 => 1403109852,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '4200542e382040a6f3-30983907',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'locale_code' => 0,
    'wireframe' => 0,
    'head_tag' => 0,
    'rss_feed' => 0,
    'logged_user' => 0,
    'content_for_layout' => 0,
    'request' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_542e38211a16f7_03092652',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_542e38211a16f7_03092652')) {function content_542e38211a16f7_03092652($_smarty_tpl) {?><?php if (!is_callable('smarty_function_image_url')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.image_url.php';
if (!is_callable('smarty_block_lang')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/globalization/helpers\\block.lang.php';
if (!is_callable('smarty_function_brand')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.brand.php';
if (!is_callable('smarty_function_implode')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.implode.php';
if (!is_callable('smarty_function_template_vars_to_js')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\function.template_vars_to_js.php';
if (!is_callable('smarty_modifier_json')) include 'C:\\wamp\\www\\dev\\activecollab/4.2.6/angie/frameworks/environment/helpers\\modifier.json.php';
?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo clean($_smarty_tpl->tpl_vars['locale_code']->value,$_smarty_tpl);?>
" lang="<?php echo clean($_smarty_tpl->tpl_vars['locale_code']->value,$_smarty_tpl);?>
" class="loading">
<head>
  <!-- Meta -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" >
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />

<?php if (activeCollab::getBrandingRemoved()){?>
  <meta name="msapplication-TileImage" content="<?php echo smarty_function_image_url(array('name'=>'layout/windows-tiles/not-branded.png','module'=>@SYSTEM_MODULE,'interface'=>@AngieApplication::INTERFACE_DEFAULT),$_smarty_tpl);?>
">
<?php }else{ ?>
  <meta name="msapplication-TileImage" content="<?php echo smarty_function_image_url(array('name'=>'layout/windows-tiles/branded.png','module'=>@SYSTEM_MODULE,'interface'=>@AngieApplication::INTERFACE_DEFAULT),$_smarty_tpl);?>
">
<?php }?>
  <meta name="msapplication-TileColor" content="#95000">

  <title><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Loading<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
...</title>
	<link rel="shortcut icon" href="<?php echo smarty_function_brand(array('what'=>'favicon'),$_smarty_tpl);?>
" type="image/x-icon" />

  <!--[if IE]>
  <script language="javascript" type="text/javascript" src="<?php echo clean($_smarty_tpl->tpl_vars['wireframe']->value->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT,AngieApplication::getDeviceClass(),'ie',true),$_smarty_tpl);?>
"></script>
  <![endif]-->
  <!--[if lte IE 9]>
    <script language="javascript" type="text/javascript" src="<?php echo clean($_smarty_tpl->tpl_vars['wireframe']->value->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT,AngieApplication::getDeviceClass(),'ie9',true),$_smarty_tpl);?>
"></script>
  <![endif]-->
  
  <!-- Head tags -->
  <?php if ($_smarty_tpl->tpl_vars['wireframe']->value->getAllHeadTags()){?>
    <?php  $_smarty_tpl->tpl_vars['head_tag'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['head_tag']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['wireframe']->value->getAllHeadTags(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['head_tag']->key => $_smarty_tpl->tpl_vars['head_tag']->value){
$_smarty_tpl->tpl_vars['head_tag']->_loop = true;
?>
      <?php echo $_smarty_tpl->tpl_vars['head_tag']->value;?>

    <?php } ?>
  <?php }?>

  <?php if (is_foreachable($_smarty_tpl->tpl_vars['wireframe']->value->getRssFeeds())){?>
    <?php  $_smarty_tpl->tpl_vars['rss_feed'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['rss_feed']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['wireframe']->value->getRssFeeds(); if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['rss_feed']->key => $_smarty_tpl->tpl_vars['rss_feed']->value){
$_smarty_tpl->tpl_vars['rss_feed']->_loop = true;
?>
      <link rel="alternate" type="<?php echo clean($_smarty_tpl->tpl_vars['rss_feed']->value['feed_type'],$_smarty_tpl);?>
" title="<?php echo clean($_smarty_tpl->tpl_vars['rss_feed']->value['title'],$_smarty_tpl);?>
" href="<?php echo clean($_smarty_tpl->tpl_vars['rss_feed']->value['url'],$_smarty_tpl);?>
" />
    <?php } ?>
  <?php }?>

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
      background: url('<?php echo @ASSETS_URL;?>
/images/environment/default/layout/login/loader.gif') no-repeat center center;
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

  <?php if (!($_smarty_tpl->tpl_vars['wireframe']->value->getInitParams($_smarty_tpl->tpl_vars['logged_user']->value) instanceof User)){?>
      <!-- *%NOT LOGGED IN%* -->
  <?php }?>

</head>

<body class="<?php echo smarty_function_implode(array('values'=>$_smarty_tpl->tpl_vars['wireframe']->value->getBodyClasses(),'separator'=>' '),$_smarty_tpl);?>
">

  <div id="page_preloader">
    <h3><?php $_smarty_tpl->smarty->_tag_stack[] = array('lang', array()); $_block_repeat=true; echo smarty_block_lang(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>
Loading<?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_lang(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
 ...</h3>
    <div id="page_preloader_progressbar"></div>
  </div>

  <script type="text/javascript">
      // initial variables
      var initial_variables = <?php echo smarty_function_template_vars_to_js(array('wireframe'=>$_smarty_tpl->tpl_vars['wireframe']->value,'wrap'=>false),$_smarty_tpl);?>
;

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
            'init_params'       : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['wireframe']->value->getInitParams($_smarty_tpl->tpl_vars['logged_user']->value));?>
,
            'loaded_content'    : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['content_for_layout']->value);?>
,
            'loaded_url'        : <?php echo smarty_modifier_json($_smarty_tpl->tpl_vars['request']->value->getUrl());?>

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
        load_script('<?php echo $_smarty_tpl->tpl_vars['wireframe']->value->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT,AngieApplication::getDeviceClass(),$_smarty_tpl->tpl_vars['wireframe']->value->getAssetsContext());?>
', function () {
          javascript_loaded = true; initialize_page();
        }, 'main_javascript');

        load_style_sheet('<?php echo $_smarty_tpl->tpl_vars['wireframe']->value->getCollectedStylesheetsUrl(AngieApplication::INTERFACE_DEFAULT,AngieApplication::getDeviceClass(),$_smarty_tpl->tpl_vars['wireframe']->value->getAssetsContext());?>
', function () {
          stylesheets_loaded = true;
          initialize_page();
        }, null, 'main_stylesheet');
      } // if

  </script>
</body>
</html><?php }} ?>