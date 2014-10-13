{title}Browse Directory{/title}

{add_bread_crumb url=$project_object_repository->getBrowseUrl($active_revision, $active_file)}{$active_file_basename}{/add_bread_crumb}
{add_bread_crumb}Browse{/add_bread_crumb}

{if $no_data}
  <p class="empty_page"><span class="inner">{lang update_url=$project_object_repository->getUpdateUrl()}There are no commits in the database for this repository/branch.<br />Would you like to <a href=":update_url" class="repository_ajax_update" title="Update">update</a> this repository/branch{/lang}?</span></p>
{else}
  {object object=$active_commit user=$logged_user repository=$project_object_repository}
    <input type="hidden" id="folder_open_icon_url" value="{image_url name='icons/16x16/folder-opened.png' module=$smarty.const.SOURCE_MODULE}" />
    <input type="hidden" id="folder_closed_icon_url" value="{image_url name='icons/16x16/folder-closed.png' module=$smarty.const.SOURCE_MODULE}" />

    <div class="wireframe_content_wrapper source_navbar">
      <h3>{lang}Browsing Repository{/lang}</h3>
      {change_repository_revision url=$browse_url repository=$active_repository test_url=$change_revision_url value=""}
    </div>

    <div id="repository_browse" class="wireframe_content_wrapper">
      <div class="ticket main_object browse_directory" id="file_">
        <div class="resources">
          <div class="source_container">
            <table class="source_directory_browser common" id="source_directory_browser" cellspacing="0">
              <tr>
                <th>{lang}Name{/lang}</th>
                <th class="file_size">{lang}Size{/lang}</th>
                <th class="date">{lang}Date{/lang}</th>
                <th class="author">{lang}Commited By{/lang}</th>
                <th class="revision">{lang}Revision{/lang}</th>
              </tr>
              <tr>
                <td colspan="5" class="loading_repository_tree">
                  {lang}Loading repository tree{/lang}... <img id="first_indicator" alt="{lang}Loading{/lang}..." />
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  {/object}
{/if}

<script type="text/javascript">

  /**
   * Async load new subtree from server
   *
   * @param obj
   * @return null
   */
  var load_tree_async = function(obj) {
    //initializing variables form tag attributes
    var state = obj.attr('state');
    var folder_closed_url = $('#folder_closed_icon_url').attr('value');
    var folder_open_url = $('#folder_open_icon_url').attr('value');
    var loaded = obj.attr('loaded');
    var toggle_key = obj.attr('toggle_key');
    var toggle_url = obj.attr('toggle_url');
    var result_container = $('#result_container_'+toggle_key);
    //if directory is shrinked we will show it
    if (state == 'shrinked') {
      obj.attr('state','expanded');
      // if directory is not loaded - we will load it
      if (loaded == 'false') {
        $('#img_'+toggle_key).attr('src',App.Wireframe.Utils.indicatorUrl());
        $.get(toggle_url, function (data) {
          $('#img_'+toggle_key).attr('src',folder_open_url);
          result_container.after(data);
          $('.child_of_'+toggle_key).each(function() {
            $(this).fadeIn('slow');
            // initializing new rows
            row_init($(this).find('.toggle_tree'));
            // padding rows to the left with style
            var level = 18 * $(this).attr('level');
            $(this).find('.toggle_tree').css('padding-left',level+'px');
            $(this).find('.file_toggle_tree').css('padding-left',level+'px');
          });
        });
        obj.attr('loaded','true');
      } else {
        $('#img_'+toggle_key).attr('src',folder_open_url);
      } //if
      toggle_open_close_all_childs(toggle_key,true,true);
    } else {
      obj.attr('state','shrinked');
      toggle_open_close_all_childs(toggle_key,false,true);
      $('#img_'+toggle_key).attr('src',folder_closed_url);
    } //if
  };


  /**
   * Toggle all children
   *
   * @param String parent_toggle_key
   * @param Boolean toggle_open_close
   * @param Boolean head
   * @return null
   */
	toggle_open_close_all_childs = function(parent_toggle_key, toggle_open_close, head) {
	  if (toggle_open_close) {
	    $('.child_of_'+parent_toggle_key).show();
	    if (head) {
	      $('#result_container_'+parent_toggle_key).attr('expanded','true');
	    }
	  } else {
	    $('.child_of_'+parent_toggle_key).hide();
	    if (head) {
	      $('#result_container_'+parent_toggle_key).attr('expanded','false');
	    }
	  }
	  
	  $('#source_directory_browser').find('.child_of_'+parent_toggle_key).each(function() {
	    if (!toggle_open_close || (toggle_open_close && $(this).attr('expanded') == "true")) {
	      toggle_open_close_all_childs($(this).attr('toggle_key'),toggle_open_close,false);
	    }
	  });
	};

  /**
   * Initialise every single row
   *
   * @param jQuery obj
   * @return null
   */
  row_init = function (obj) {
    obj.click(function (ev) {
      load_tree_async(obj);
    });
  };



  var table = $('#source_directory_browser');
  $("#first_indicator").attr('src',App.Wireframe.Utils.indicatorUrl());


  var first_load_url = '{$project_object_repository->getToggleUrl($active_commit, $active_file, null)}';
  $.get(first_load_url, function (data) {
    $("#first_indicator").parent().parent().remove();
    table.append(data);
    table.find('.child_of_root:hidden').fadeIn();
    table.find('.toggle_tree').each(function() {
      row_init($(this));
    });
  });
</script>