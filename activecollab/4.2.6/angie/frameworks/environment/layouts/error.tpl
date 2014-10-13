<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en-us">
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <title>{$page_title}</title>
  <link rel="shortcut icon" href="{brand what=favicon}" type="image/x-icon" />
  <link rel="stylesheet" href="{$wireframe->getCollectedStylesheetsUrl(AngieApplication::INTERFACE_DEFAULT, AngieApplication::getDeviceClass(), $wireframe->getAssetsContext())}" type="text/css" media="screen">
</head>
<body id="error_page">
  <div id="company_logo">
    <a href="{assemble route=homepage}"><img src="{brand what=logo}" alt="" /></a>
  </div>
  <div id="error_box">
    <h1>{$page_title}</h1>

    <div class="description">
      {if $message}
        <p><strong>{$message}</strong></p>
      {/if}
      <p>Please check the URL for proper spelling and capitalization. If you're having trouble locating page or object, try using <a href="{assemble route="backend_search"}">search</a>, you can go back to <a onclick="history.back(1); return false" href="#">page you came from</a>, or alternatively you can visit <a href="{assemble route="homepage"}">homepage</a></p>
    </div>
  </div>
  <div id="footer">
    <p id="copyright">&copy;{year}</p>
  </div>
</body>
</html>