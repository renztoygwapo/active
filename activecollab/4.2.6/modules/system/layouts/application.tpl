<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$locale_code}" lang="{$locale_code}">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{page_title default="Projects"}</title>
    
    <!--  Stylesheets -->
    <link rel="stylesheet" href="{$wireframe->getCollectedStylesheetsUrl($prefered_interface, 'unknown', $wireframe->getAssetsContext())}" type="text/css" media="screen"/>
    
    <!--[if IE]><![endif]-->
    
    <!-- Scripts -->
    <script type="text/javascript" src="{$wireframe->getCollectedJavaScriptUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), $wireframe->getAssetsContext())}"></script>
    
    {template_vars_to_js wireframe=$wireframe}
    
    <!-- Head tags -->
    {if $wireframe->getAllHeadTags()}
      {foreach $wireframe->getAllHeadTags() as $head_tag}
        {$head_tag nofilter}
      {/foreach}
    {/if}

    <!-- Meta -->
    <link rel="shortcut icon" href="{brand what=favicon}" type="image/x-icon" />
  {if activeCollab::getBrandingRemoved()}
    <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/not-branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
  {else}
    <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
  {/if}
    <meta name="msapplication-TileColor" content="#95000">
    
    {if is_foreachable($wireframe->getRssFeeds())}
      {foreach $wireframe->getRssFeeds() as $rss_feed}
        <link rel="alternate" type="{$rss_feed.feed_type}" title="{$rss_feed.title}" href="{$rss_feed.url}" />
      {/foreach}
    {/if}

    {if !($wireframe->getInitParams($logged_user) instanceof User)}
      <!-- *%NOT LOGGED IN%* -->
    {/if}
  </head>
  
  <body id="login_page">
    <div id="login_splash">
      <img src="{image_url name="layout/branding/login-page-logo.png" module=$smarty.const.SYSTEM_MODULE}" alt="" />
    </div>
    <div id="content">
      <table class="vertical_aligner" cellspacing="0"><tr><td>
      {$content_for_layout nofilter}
      </td></tr></table>
    </div>
  </body>
</html>