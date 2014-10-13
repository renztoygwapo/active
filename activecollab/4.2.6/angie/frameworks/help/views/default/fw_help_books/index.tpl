{title}Books{/title}
{add_bread_crumb}Home{/add_bread_crumb}

<div id="help_books" class="wireframe_content_wrapper">
  {search_help}

  <ul id="help_books_list">
    {foreach $books as $book}
      <li>
        <a href="{$book->getUrl()}">
          <span class="book_cover"><img src="{$book->getCoverUrl()}" /></span>
          <span class="book_name">{$book->getTitle()}</span>
          <span class="book_description">{$book->getDescription()}</span>
        </a>
      </li>
    {/foreach}
  </ul>
</div>

<script type="text/javascript">
  $('#help_books').each(function() {

  });
</script>