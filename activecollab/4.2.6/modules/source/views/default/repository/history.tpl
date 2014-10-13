{add_bread_crumb}Commit History{/add_bread_crumb}

{object object=$project_object_repository user=$logged_user}
  {if $filter_by_author}
    <div class="wireframe_content_wrapper source_navbar">
      <h3>{lang clean_params=false created_by=$filter_by_author.user_object history_url=$project_object_repository->getHistoryUrl()}You are viewing commits created by <b>:created_by</b> only. Click <a href=":history_url">here</a> to view all commits made to this repository{/lang}</h3>
    </div>
  {/if}

<div id="repository_history" class="wireframe_content_wrapper">

  {if is_foreachable($commits) and $commits|@count > 0}
    <div class="history_header">

    </div>

    <div class="grouped_commits">
    {include file=get_view_path('_history_loop', 'repository', $smarty.const.SOURCE_MODULE)}
    </div>

    <div class="show_thirty_more">
      <button type="button" id ="show_thirty_more" show_thirty_more_url = "{$show_thirty_more_url}" filter_by_author = "{$filter_by_author.user_name}">{lang}Show older revisions{/lang}</button>
      <img alt="" src="" id="show_img_pending" /><p id="show_error_msg"></p>
    </div>
  {else}
    <p class="empty_page"><span class="inner">{lang update_url=$project_object_repository->getUpdateUrl()}There are no commits in the database for this repository/branch.<br />Would you like to <a href=":update_url" class="repository_ajax_update" title="Update">update</a> this repository/branch{/lang}?</span></p>
  {/if}
</div>
{/object}

<script type="text/javascript">
  App.Config.set('commit_history_offset', 1);
  $('.repository_ajax_update').flyout();
  var project_repositories_url = '{$project_repositories_url nofilter}';


  /**
   * Redirect to repository index page after removing the repository
   */

  App.Wireframe.Events.bind('project_source_repository_deleted', function (event, repository) {
    App.Wireframe.Content.setFromUrl(project_repositories_url);
  });

  /**
   * Initialize commits
   *
   * @param jQuery wrapper
   */
  var init_commits = function (wrapper) {
    wrapper.find('a.toggle_files').flyout(

    );
  }; // init_commits

  init_commits($('#repository_history'));

  var show_more_button = $('#show_thirty_more');

  var show_more_url = show_more_button.attr('show_thirty_more_url');
  var filter_by_author = show_more_button.attr('filter_by_author');

  var show_more_wrapper = show_more_button.parents('div:first');
  show_more_button.click(function () {
    var loading_row = $('<div class="loading"><img src="' + App.Wireframe.Utils.indicatorUrl() + '" alt=""/>' + App.lang('Loading ...') + '</div>').prependTo(show_more_wrapper);
    show_more_button.hide();

    var current_request_url = App.extendUrl(show_more_url, {
      'offset' : App.Config.get('commit_history_offset')
    });

    if (filter_by_author) {
      current_request_url = App.extendUrl(current_request_url, {
        'filter_by_author' : filter_by_author
      });
    } // if

    $.ajax({
      'url' : current_request_url,
      'type' : 'GET',
      'success' : function (data) {
        if (data == 'empty') {
          App.Wireframe.Flash.success(App.lang('There are no more revisions to load'));
        } else {
          var result = $(data);
          init_commits(result);
          $('div.grouped_commits').append(result);
          App.Config.set('commit_history_offset', App.Config.get('commit_history_offset') + 1);
          show_more_button.show();
        } // if
        loading_row.remove();
      }
    });
    return false;
  });



</script>