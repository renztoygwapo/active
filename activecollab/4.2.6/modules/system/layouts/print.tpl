<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$locale_code}" lang="{$locale_code}">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="{$wireframe->getCollectedPrintStylesheetsUrl()}" type="text/css" media="screen" />
    <link rel="stylesheet" href="{$wireframe->getCollectedPrintStylesheetsUrl()}" type="text/css" media="print" />
    <script type="text/javascript" src="{$wireframe->getCollectedPrintJavaScriptUrl()}"></script>
    {page_head_tags}
    <title>{page_title default="Projects"} / {$owner_company->getName()}</title>
    {template_vars_to_js wireframe=$wireframe}
    <style>
      .opera_print {
        text-align: right;
        margin: 0;
        padding: 5px;
        border-bottom: 1px solid #000;
        background:#ccc;
      }

      @media print {
        .opera_print {
          display: none;
        }
      }
    </style>
  </head>
  <body>
    <div class="page_header" id="page_header"></div>
    <h1 class="page_title" id="page_title">{$wireframe->getPageTitle()}</h1>
    <div class="page_content" id="page_content">{$content_for_layout nofilter}</div>
    <div class="page_footer" id="page_footer"></div>
  </body>
  <script type="text/javascript">
    if (!$.browser.opera) {
      setTimeout(function () {
        window.focus();
        window.print();
        hidePrintOverlay();
      }, 1000);
    } else {
      $('body').prepend('<p class="opera_print"><input type="button" onclick="window.print();window.close()" value="' + App.lang('Print') + '"/></p>');
      var clone = document.documentElement.cloneNode(true)
      var win = window.open('about:blank');
      win.document.replaceChild(clone, win.document.documentElement);
      hidePrintOverlay();
    } // if

    /**
     * Hide "preparing for printing overlay"
     */
    function hidePrintOverlay() {
      if (window.parent && window.parent.App && window.parent.App.Wireframe && window.parent.App.Wireframe.Print) {
        window.parent.App.Wireframe.Print.close();
      } // if
    } // hidePrintOverlay
  </script>
</html>