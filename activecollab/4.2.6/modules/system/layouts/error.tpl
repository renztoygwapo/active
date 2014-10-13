<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$locale_code}" lang="{$locale_code}">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>{page_title default="Error"}</title>
    <link rel="shortcut icon" href="{image_url name='favicon.png'}" type="image/x-icon" />
  </head>
  <body>
    <div id="company_logo">
    <a href="{assemble route=homepage}"><img src="{brand what=logo}" alt="" /></a>
    </div>
    <div id="error_box">
      {$content_for_layout nofilter}
    </div>
    <div id="footer">
      <p id="copyright">&copy;{year}</p>
    </div>
  </body>
</html>