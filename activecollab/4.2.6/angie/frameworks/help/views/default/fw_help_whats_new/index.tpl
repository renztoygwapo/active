{title}New and Newsworthy{/title}
{add_bread_crumb}New and Newsworthy{/add_bread_crumb}

<div id="help_whats_new" class="help_content_page">
  <div id="help_whats_new_articles_list" class="help_content_page_sidebar">
  {foreach $articles_by_version as $version => $version_articles}
    <p>{$version}</p>
    <ol>
    {foreach $version_articles as $article}
      <li {if $selected_article && $selected_article == $article->getSlug()}class="selected"{/if}><a href="{$article->getUrl()}" data-short-name="{replace search='-' in=$article->getShortName() replacement='_'}">{$article->getTitle()}</a>{if AngieApplication::help()->isNewSinceLastUpgrade($article)} <span class="new_since_last_upgrade" title="{lang}New Since Your Last Upgrade{/lang}">{lang}New{/lang}</span>{/if}</li>
    {/foreach}
    </ol>
  {/foreach}
  </div>

  <div id="help_whats_new_articles" class="help_content_page_content">
    <div id="help_whats_new_articles_content" class="help_content_page_content_wrapper">
      {foreach $articles as $article}
        <div class="help_whats_new_article help_element" data-short-name="{replace search='-' in=$article->getShortName() replacement='_'}" style="display: none">
          <div class="help_new_in">{lang version=$article->getVersionNumber()}New in v:version{/lang}</div>
          <h1>{$article->getTitle()}</h1>
          <div class="help_whats_new_article_content help_element_content">{AngieApplication::help()->renderBody($article) nofilter}</div>
        </div>
      {/foreach}
    </div>
  </div>
</div>

<script type="text/javascript">
  var wrapper = $('#help_whats_new');
  var page_title = '{lang}New and Newsworthy{/lang}';

  /**
   * Show article
   *
   * @param String short_name
   */
  var do_show_article = function (short_name) {
    var page = wrapper.find('#help_whats_new_articles div.help_whats_new_article[data-short-name=' + short_name + ']');
    var link = wrapper.find('#help_whats_new_articles_list a[data-short-name=' + short_name + ']');

    if(page.length > 0) {
      var selected_article = wrapper.find('div.help_whats_new_article:visible');

      if(selected_article.length > 0) {
        if(selected_article.data('shortName') == page.data('shortName')) {
          return false;
        } // if

        wrapper.find('#help_whats_new_articles_list li').removeClass('selected');
        link.parent().addClass('selected');

        $('#help_whats_new_articles').scrollTo(0, 200, {
          'onAfter' : function() {
            selected_article.fadeOut({
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
    if (handler != 'whats_new') {
      return true;
    } // if

    App.Wireframe.PageTitle.set(page_title);
    do_show_article(state.data['additional'] ? state.data['additional']['page_short_name'] : null);

    History.handled = true;
    return false;
  });

  wrapper.each(function() {
    wrapper.on('click', '#help_whats_new_articles_list a', function(event, is_initial) {
      var anchor = $(this);

      // push history state
      History.pushState({
        'handler' : 'whats_new',
        'additional' : {
          'page_short_name'  : anchor.attr('data-short-name')
        }
      }, App.lang('Loading'), anchor.attr('href'));

      return false;
    });

    // Open first article
    var selected_item = wrapper.find('#help_whats_new_articles_list li.selected a');

    if(selected_item.length > 0) {
      do_show_article(selected_item.attr('data-short-name'));
    } else {
      wrapper.find('#help_whats_new_articles_list li:first a:first').each(function() {
        do_show_article($(this).attr('data-short-name'));
      });
    } // if
  });
</script>