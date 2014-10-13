<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$locale_code}" lang="{$locale_code}">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" >
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

  {if activeCollab::getBrandingRemoved()}
    <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/not-branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
    {else}
    <meta name="msapplication-TileImage" content="{image_url name='layout/windows-tiles/branded.png' module=$smarty.const.SYSTEM_MODULE interface=$smarty.const.AngieApplication::INTERFACE_DEFAULT}">
  {/if}
    <meta name="msapplication-TileColor" content="#95000">
    <meta name="robots" content="noindex, nofollow">

    <title>{page_title default="Index"}</title>
    
    <!--  Stylesheets -->
    <link rel="stylesheet" href="{$wireframe->getCollectedStylesheetsUrl($prefered_interface, 'unknown', $wireframe->getAssetsContext())}" type="text/css" media="screen"/>
    
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
    
    {if is_foreachable($wireframe->getRssFeeds())}
      {foreach $wireframe->getRssFeeds() as $rss_feed}
        <link rel="alternate" type="{$rss_feed.feed_type}" title="{$rss_feed.title}" href="{$rss_feed.url}" />
      {/foreach}
    {/if}
  </head>
  <body>
    <div id="public_header"><div class="public_wrapper">
      <img src="{brand what="logo"}" alt="Logo"/>
    </div></div>
    
    <div id="public_page_title"><div class="public_wrapper">
      <h1>{page_title default="Index"}</h1>
    </div></div>
    
    <div id="public_content"><div class="public_wrapper">
    	{$content_for_layout nofilter}
    </div></div>
    
    <div id="public_footer"><div class="public_wrapper">
      {lang}Powered by{/lang}<br />
      <img src="{brand what="logo" size="40x40"}" alt="Logo" class="ac_logo"/>
    </div></div>
  </body>
</html>