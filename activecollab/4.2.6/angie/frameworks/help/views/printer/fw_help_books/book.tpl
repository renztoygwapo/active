{title name=$book->getTitle()}Book ":name"{/title}

<div id="help_book" class="help_content_page">
  <div id="help_book_pages_list" class="help_content_page_sidebar">
    <h2>{lang}Table of Contents{/lang}</h2>

    <ol>
    {foreach $book->getPages($logged_user) as $page}
      <li>{$page->getTitle()}</li>
    {/foreach}
    </ol>
  </div>

  <div id="help_book_pages" class="help_content_page_content">
    <div class="help_content_page_content_wrapper">
      {foreach from=$book->getPages($logged_user) item=page name=help_books}
        <div class="help_book_page help_element" data-short-name="{$page->getShortName()}">
          <h2>{counter name='book_page'}. {$page->getTitle()}</h2>
          <div class="help_book_page_content help_element_content">{AngieApplication::help()->renderBody($page) nofilter}</div>
        </div>
      {/foreach}
    </div>
  </div>
</div>