{title name=$book->getTitle()}Book ":name"{/title}
{add_bread_crumb}{$book->getTitle()}{/add_bread_crumb}

<div id="help_book" class="help_content_page">
  <div id="help_book_pages_list" class="help_content_page_sidebar">
    <div id="help_book_cover">
      <img src="{$book->getCoverUrl()}" />
    </div>

    <ol>
    {foreach $book->getPages($logged_user) as $page}
      <li {if $selected_page && $selected_page == $page->getShortName()}class="selected"{/if}><a href="{$page->getUrl()}" data-short-name="{$page->getShortName()}">{$page->getTitle()}</a></li>
    {/foreach}
    </ol>
  </div>

  <div id="help_book_pages" class="help_content_page_content">
    <div class="help_content_page_content_wrapper">
      {foreach from=$book->getPages($logged_user) item=page name=help_books}
        <div class="help_book_page help_element" data-short-name="{$page->getShortName()}" style="display: none">
          <h1>{$page->getTitle()}</h1>
          <div class="help_book_page_content help_element_content">{AngieApplication::help()->renderBody($page) nofilter}</div>

          <div class="help_book_footer">
            <div class="help_book_footer_inner">
              <div class="help_book_footer_prev">{if !$smarty.foreach.help_books.first}<a href="#">&laquo; {lang}Prev{/lang}</a>{/if}</div>
              <div class="help_book_footer_top"><a href="#">{lang}Back to the Top{/lang}</a></div>
              <div class="help_book_footer_next">{if !$smarty.foreach.help_books.last}<a href="#">{lang}Next{/lang} &raquo;</a>{/if}</div>
            </div>
          </div>
        </div>
      {/foreach}
    </div>
  </div>
</div>

<script type="text/javascript">
  var book_id = {$book->getShortName()|json nofilter};
  var wrapper = $('#help_book');
  var page_title = {$book->getTitle()|json nofilter};

  /**
   * Do show article
   */
  var do_show_article = function (short_name) {
    var page = wrapper.find('#help_book_pages div.help_book_page[data-short-name=' + short_name + ']');
    var link = wrapper.find('#help_book_pages_list a[data-short-name=' + short_name + ']');

    if(page.length > 0) {
      var selected_page = wrapper.find('div.help_book_page:visible');

      if(selected_page.length > 0) {
        if(selected_page.data('shortName') == page.data('shortName')) {
          return false;
        } // if

        wrapper.find('#help_book_pages_list li').removeClass('selected');
        link.parent().addClass('selected');

        $('#help_book_pages').scrollTo(0, 200, {
          'onAfter' : function() {
            selected_page.fadeOut({
              'complete' : function() {
                page.fadeIn();
              }
            });
          }
        });
      } else {
        page.show();
        link.parent().addClass('selected');
      } // if
    } // if
  };

  // Handle history requests
  App.Wireframe.Events.bind('history_state_changed.content', function (event) {
    var state = History.getState();
    var handler = state.data['handler'];
    var handler_id = state.data['handler_id'];

    // this is not handler we're looking for
    if (handler != 'help_book') {
      return true;
    } // if

    // this is some other handler of same type
    if (book_id != handler_id) {
      return true;
    } // if

    App.Wireframe.PageTitle.set(page_title);

    do_show_article(state.data['additional'] ? state.data['additional']['page_short_name'] : null);

    History.handled = true;
    return false;
  });

  wrapper.each(function() {

    wrapper.on('click', '#help_book_pages_list a', function() {
      var anchor = $(this);

      // push history state
      History.pushState({
        'handler' : 'help_book',
        'handler_id' : book_id,
        'additional' : {
          'page_short_name'  : anchor.attr('data-short-name')
        }
      }, App.lang('Loading'), anchor.attr('href'));

      return false;
    });

    /**
     * Highlight elements in the interface on hover
     */

    wrapper.on('mouseenter', 'span.option', function() {
      var option_for_element = $(this).attr('for');

      if(option_for_element) {
        var element = $('#' + option_for_element + ':visible');

        if(element.length > 0) {
          if(element[0].help_highlight_timer) {
            return;
          } // if

          element[0].help_highlight_timer = setTimeout(function() {
            element.highlightFade();

            // Clear and unset
            clearTimeout(element[0].help_highlight_timer);
            element[0].help_highlight_timer = null;
          }, 300);
        } // if
      } // if
    });

    wrapper.on('mouseleave', 'span.option', function() {
      var option_for_element = $(this).attr('for');

      if(option_for_element) {
        var element = $('#' + option_for_element + ':visible');

        if(element.length > 0 && element[0].help_highlight_timer) {
          clearTimeout(element[0].help_highlight_timer);

          // Clear and unset
          element[0].help_highlight_timer = null;
        } // if
      } // if
    });

    wrapper.on('click', 'a.link_to_help_book_page', function() {
      var link = $(this);

      if(link.data('bookName') == '{$book->getShortName()}') {
        wrapper.find('#help_book_pages_list li a[data-short-name=' + link.data('pageName') + ']').click();
        return false;
      } // if

      return true;
    });

    wrapper.on('click', 'div.help_book_footer_prev a', function() {
      var list_item = wrapper.find('#help_book_pages_list li a[data-short-name=' + $(this).parents('div.help_book_page').data('shortName') + ']').parent();

      var prev_item = list_item.prev();

      if(prev_item.length) {
        prev_item.find('a').click();
      } // if

      return false;
    });

    wrapper.on('click', 'div.help_book_footer_top a', function() {
      $('#help_book_pages').scrollTo(0, 200);
      return false;
    });

    wrapper.on('click', 'div.help_book_footer_next a', function() {
      var list_item = wrapper.find('#help_book_pages_list li a[data-short-name=' + $(this).parents('div.help_book_page').data('shortName') + ']').parent();

      var next_item = list_item.next();

      if(next_item.length) {
        next_item.find('a').click();
      } // if

      return false;
    });

    // Open the first page
    var selected_page = wrapper.find('#help_book_pages_list li.selected');
    if(selected_page.length > 0) {
      do_show_article(selected_page.find('a').attr('data-short-name'));
    } else {
      do_show_article(wrapper.find('#help_book_pages_list li:first a:first').attr('data-short-name'));
    } // if
  });
</script>