<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

{form action=$project_object_repository->getFileCompareUrl($active_file) method="POST" autofocus=yes ask_on_leave=yes class='compare_revision_form' id='compare_revision_form'}
  <div class="compare_revision_form_column">
    <div class="compare_revision_header">
    	<table class="compare_revisions_table" cellspacing="0">
        <tr>
          <th class="revision_checkbox">{lang}Comp.{/lang}</th>
          <th class="revision_number">{lang}Revision{/lang}</th>
          <th class="revision_details">{lang}Comment{/lang}</th>
          <th class="revision_date">{lang}Date{/lang}</th>
          <th class="revision_author">{lang}Author{/lang}</th>
        </tr>
      </table>
    </div>    
    <div class="compare_revision_list">
    	<table class="compare_revisions_table" cellspacing="0">
        {foreach from=$commits name=commit_list item=commit}
        <tr class="{cycle values='odd,even'} compare_revisions_table_left" commit_info_url ="{$commit->getOneCommitInfoUrl($active_project->getSlug(), $project_object_repository->getId())}" revision="{$commit->getRevisionNumber()}">
          <td class="revision_checkbox">
            <input type="checkbox" value="{$commit->getRevisionNumber()}" />
          </td>
          <td class="revision_number">
            <a href="{$project_object_repository->getBrowseUrl($commit, $active_file)}" title="{lang}View details{/lang}" class="number">{substr($commit->getName(),0,8)}</a><br />
          </td>
          <td class="revision_details">
            {$commit->getMessageBody()|nl2br|clickable|stripslashes|excerpt:80 nofilter}
          </td>
          <td class="revision_date">
            {$commit->getCommitedOn()|date:0}
          </td>
          <td class="revision_author">
            {$commit->getAuthor($active_repository) nofilter}
          </td>
        </tr>
        {/foreach}
      </table>
    </div>
    <div class="compare_revision_selected_revision">
      {lang}Choose revision to see its details{/lang}
    </div>
  </div>

   {wrap_buttons}
    {submit disabled=disabled}Compare Revisions{/submit}
  {/wrap_buttons}
{/form}

{literal}
<script type="text/javascript">
  var form = $('.compare_revision_form');
  var history_item_details = form.find('.compare_revision_selected_revision');
  var history_table = form.find('.compare_revision_list table');
  var history_table_rows = form.find('.compare_revision_list table tr');

  var history_table_button = form.find('.button_holder button:first');
  var compare_from;
  var compare_to;

  /**
   * What happens when row is clicked
   * 
   * @param jQuery current_row
   * @return null
   */
  var row_click = function(current_row) {
    history_table_rows.removeClass('selected');
    current_row.addClass('selected');

    history_item_details.html('<img alt="Pending..." src="' + App.Wireframe.Utils.indicatorUrl() + '" />' + App.lang('Loading') + '...').addClass('loading');

    $.ajax({
      url : current_row.attr('commit_info_url'),
      success : function (response) {
    		if (response == 'empty') {
  		    history_item_details.html(App.lang('Error loading commit info. Please try again')).removeClass('loading');
    		} else {
  		    history_item_details.html(response).removeClass('loading');
    		} // if
      },
      error : function () {
        history_item_details.html(App.lang('Error loading commit info. Please try again')).removeClass('loading');
      }
    });
  }; // row_click

  /**
   * What happens when checkbox is clicked
   * 
   * @param jQuery current_checkbox
   * @return null
   */
  var checkbox_click = function (current_checkbox) {
    // if there are already two checboxes selected unselect the currently clicked one
    if (current_checkbox.is(':checked')) {  
      if (history_table.find('input[type=checkbox]:checked').length > 2) {
        current_checkbox.removeAttr('checked');  
      } // if
    } // if

    var selected_checkboxes = history_table.find('input[type=checkbox]:checked'); 
    if (selected_checkboxes.length == 2) {
      
      compare_from = selected_checkboxes.eq(0).val();
      compare_to = selected_checkboxes.eq(1).val();
      
      history_table_button.attr('disabled', false);
    } else {
      history_table_button.attr('disabled', true);
    } // if
  }; // checkbox_click

  /**
   * Load diff for selected file and revisions
   *
   * @param String url
   * @param String revision_from
   * @param String revision_to
   */
  var load_diff = function (url, revision_from, revision_to) {
    var final_url = App.extendUrl(url, {
      'rev_compare_from' : revision_from,
      'rev_compare_to' : revision_to
    })
    
    var diff_container = $('<div class="compare_diff_container"></div>').appendTo(form);
    diff_container.html('<img alt="Pending..." src="' + App.Wireframe.Utils.indicatorUrl() + '" />' + App.lang('Loading') + '...').addClass('loading');

    $.ajax({
      'url' : final_url,
      'success' : function (data) {
        diff_container.html(data).removeClass('loading');

        diff_container.find('.repository_compare_files_back a:first').click(function () {
          diff_container.remove();
          return false;
        });
      },
      'error' : function (data, response) {
        diff_container.remove();
        App.Wireframe.Flash.error(App.lang('Failed to load diff'));
      }
    });
  }; // load_diff

  
  history_table.click(function (event) {
    var original_target = $(event.target);

    // row clicked
    if (original_target.is('td')) {
      row_click(original_target.parents('tr:first'));

    // checkbox clicked
    } else if (original_target.is('input[type=checkbox]')) {
      checkbox_click(original_target);
    } // if
  });

  history_table_button.click(function () {
    load_diff(form.attr('action'), compare_from, compare_to);
    return false;
  });

</script>
{/literal}