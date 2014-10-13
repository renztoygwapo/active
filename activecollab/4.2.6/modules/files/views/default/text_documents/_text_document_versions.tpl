{use_widget name="text_compare_dialog" module="text_compare"}

<div class="file_versions" id="{$_document_versions_id}" text_document_version="{$_document_version_num}">
  <div class="content_section_title"><h2>{lang}Text Document History{/lang}</h2></div>
  <div class="content_section_containter">
      <table class="file_versions common" cellspacing="0">
      <thead>
      <tr>
        <th>{lang}Version{/lang}</th>
        <th>{lang}Details{/lang}</th>
        <th></th>
      </tr>
      </thead>
      <tbody>
	      <tr class="file_version latest {cycle values='odd,even'}">
	        <td class="version"><a href="{$_document_versions_document->getViewUrl()}">{lang}Latest Version{/lang}</a></td>
	        <td class="details">
	          {$_document_versions_document->getLastVersionOn()|ago nofilter} {lang}by{/lang} {user_link user=$_document_versions_document->getLastVersionBy()}
	        </td>
	        <td class="options">
	          <span class="latest_document_version">{lang}Latest{/lang}</span>
	        </td>
	      </tr>
		    {if is_foreachable($_document_versions)}
		      {foreach from=$_document_versions item=_document_version name=document_revisions}
		      <tr class="file_version {cycle values='odd,even'}">
		        <td class="version"><a href="{$_compare_versions_url} " class="compare_versions" right_version_num="{$_document_version->getVersionNum()}" left_version_num="{$_document_versions_document->getVersionNum()}">#{$_document_version->getVersionNum()}</a></td>
		        <td class="details">{$_document_version->getCreatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_document_version->getCreatedBy()}</td>
		        <td class="options">
              {if $_document_versions_document->canEdit($_document_versions_user)}<a version_num = "{$_document_version->getVersionNum()}" href="{$_document_versions_document->getRevertUrl($_document_version)}" title="{lang}Revert to Version{/lang}" class="revert_version"><img src="{image_url name="icons/12x12/swap.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>{/if}
		          {if $_document_version->canDelete($_document_versions_user)}<a href="{$_document_version->getDeleteUrl()}" title="{lang}Delete Permanently{/lang}" class="remove_version"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>{/if}
		        </td>
		      </tr>
		      {/foreach}
		    {/if}
      </tbody>
    </table>
  </div>
</div>

<script type="text/javascript">
  $('#{$_document_versions_id}').each(function() {
    var wrapper = $(this);
    var versions_table = wrapper.find('table.file_versions');

    /**
     * Resets and fill the version compare widget
     *
     * @param all_versions - all versions before latest
     * @param latest_version - latest version of text document
     */
    var init_compare_versions = function (all_versions, latest_version) {
      App.widgets.TextCompareDialog.reset();
      // other versions
      if (all_versions) {
        $.each(all_versions, function(key, version) {
          App.widgets.TextCompareDialog.addVersion({
            'version' : version['version'],
            'name' : version['name'],
            'body' : version['body']
          });
        });
      } //if
      // current version
      App.widgets.TextCompareDialog.addVersion({
        'version' : latest_version['version'],
        'name' : latest_version['name'],
        'body' : latest_version['body']
      });
      $('a.compare_versions').click(function() {
        App.widgets.TextCompareDialog.compareVersions(this);
        return false;
      });
    }; //init_compare_versions


    var current_version = {$_document_versions_document|json nofilter};
    var all_versions = {$_document_versions|json nofilter};


    init_compare_versions(all_versions, current_version);

    
    /**
     * Initialize row
     *
     * @param jQuery row
     * @return null
     */
    var init_row = function(row) {
      row.find('a.remove_version').asyncLink({
        'confirmation' : App.lang('Are you sure that you want to permanently remove this document version?'),
        'indicator_url' : App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-small.gif', 'environment'),
        'success' : function() {
          row.remove();
          App.Wireframe.Flash.success('Document version successfully removed');
        }, 
        'error' : function() {
          App.Wireframe.Flash.error('Failed to remove selected document version. Please try again later');
        }
      });
      row.find('a.revert_version').each(function(index, value) {
        $(this).asyncLink({
          'confirmation' : App.lang('Are you sure that you want to revert to selected version?'),
          'indicator_url' : App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-small.gif', 'environment'),
          'success_event' : 'asset_updated',
          'success_message' : App.lang('Page content successfully reverted to version #:version_num', {
            'version_num' : $(this).attr('version_num')
          })
        });
      });


    }; // init_row

    // initialize every row
    versions_table.find('tr').each(function() {
      init_row($(this));
    });

    // Asset updated
    App.Wireframe.Events.bind('asset_updated.{$request->getEventScope()}', function (event, asset) {
      var compare_versions_url = {$_compare_versions_url|json nofilter};
      if (asset['class'] == 'TextDocument' && asset['version'] != wrapper.attr('text_document_version')) {
        wrapper.attr('text_document_version', asset['version']);
        
        var table_body = versions_table.find('tbody:first');
        table_body.empty();
        
        var versions = asset.versions;
        if (versions && versions.length) {
          $.each(versions, function (index, version) {
            var row = $('<tr class="file_version latest">' +
              '<td class="version"><a class="compare_versions" right_version_num="' + version.version + '" left_version_num="' + asset.version + '" href="' + compare_versions_url + '">' + App.lang('#:version_num', { version_num : version.version }) + '</a></td>' +
              '<td class="details"><span title="' + version.created_on.formatted + '" class="ago">' + App.Wireframe.Utils.ago(version.created_on) + '</span> ' + App.lang('by') + ' <a class="user_link" href="' + version.created_by.permalink + '">' + App.clean(version.created_by.display_name) + '</a></td>' +
              '<td class="options">' +
                '<a class="revert_version" title="Revert to Version" version_num = ' + version.version + ' href="' + App.clean(asset.urls['revert'].replace('--REVERT-TO-VERSION--', version.version)) + '"><img alt="" src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/swap.png', 'environment') + '"></a>' +
                '<a class="remove_version" title="Delete Permanently" href="' + version.urls['delete'] + '"><img alt="" src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment') + '"></a>' +
              '</td>' +
            '</tr>').appendTo(table_body);
            init_row(row);
          });
        } // if

        var latest_row = $('<tr class="file_version latest">' +
            '<td class="version"><a  href="' + asset.urls.view + '">' + App.lang('Latest Version') + '</a></td>' +
            '<td class="details"><span title="' + asset.last_version_on.formatted + '" class="ago">' + App.Wireframe.Utils.ago(asset.last_version_on) + '</span> ' + App.lang('by') + ' <a class="user_link" href="' + asset.last_version_by.permalink + '">' + App.clean(asset.last_version_by.display_name) + '</a></td>' +
            '<td class="options"><span class="latest_file_version">' + App.lang('Latest') + '</span></td>' +
          '</tr>');
          
        latest_row.prependTo(table_body);
        init_compare_versions(versions, asset)
      } // if
    });
  });
</script>