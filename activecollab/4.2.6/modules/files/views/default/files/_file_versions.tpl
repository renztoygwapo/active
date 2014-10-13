{use_widget name="plupload" module="file_uploader"}

{if $_file_versions_wrap}
<div class="file_versions" id="{$_file_versions_id}">
{/if}

  {if $_file_versions_can_add}
  <div class="section_button_wrapper">
    <a href="#" class="upload_new_file_version section_button"><span><img src="{image_url name='icons/16x16/new-file-version-section-button.png' module=$smarty.const.FILES_MODULE}" />{lang}Upload a New Version{/lang}</span></a>
  </div>
  {/if}

  <div class="content_section_container">
  
    <!-- New File Version Form -->
    {if $_file_versions_file->canUploadNewVersions($_file_versions_user)}
    <div class="upload_new_file_version">
      <form action="{$_file_versions_upload_new_version_url}" method="post" enctype="multipart/form-data">

        {wrap field=new_file_version}
          {label for=newVersionFile required=yes}Upload New File Version{/label}
          <div id="new_file_version_uploader_wrapper">
            <span class="control_wrapper">
              <span class="file_upload_label">{lang}No File Selected{/lang}</span>
              <span class="upload_progressbar"><span class="upload_progressbar_bar"></span></span>
            </span>
            <a id="new_file_version_uploader_wrapper_button" href="#" class="link_button" ><span class="inner"><span class="icon button_add">{lang}Choose File{/lang}</span></span></a>
          </div>

	        <p class="details">{max_file_size_warning}</p>
        {/wrap}
        
        {wrap_buttons}
          {button type="submit"}Upload{/button} {lang}or{/lang} <a href="#" class="upload_new_file_version_cancel">{lang}Cancel{/lang}</a>
        {/wrap_buttons}
        
        <input type="hidden" name="submitted" value="submitted" />
      </form>
    </div>
    {/if}
    
    <!-- File Versions List -->
    <table class="file_versions common" cellspacing="0">
      <thead>
	      <tr>
	        <th>{lang}Version{/lang}</th>
	        <th>{lang}Size{/lang}</th>
	        <th>{lang}Details{/lang}</th>
	        <th></th>
	      </tr>
      </thead> 
      
      <tbody>
	      <tr class="file_version latest {cycle values='odd,even'}">
	        <td class="version"><a href="{$_file_versions_file->getDownloadUrl(true)}" target="_blank">{lang}Latest Version{/lang}</a></td>
	        <td class="size">{$_file_versions_file->getSize()|filesize}</td>
	        <td class="details">
            {$_file_versions_file->getLastVersionOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_versions_file->getLastVersionBy()}
	        </td>
	        <td class="options">
	          <span class="latest_file_version">{lang}Latest{/lang}</span>
	        </td>
	      </tr>
	    {if is_foreachable($_file_versions)}
	      {foreach from=$_file_versions item=_file_version name=file_revisions}
	      <tr class="file_version {cycle values='odd,even'}">
	        <td class="version"><a href="{$_file_version->getViewUrl()}" target="_blank">{lang}Version #{/lang}{$_file_version->getVersionNum()}</a></td>
	        <td class="size">{$_file_version->getSize()|filesize}</td>
	        <td class="details">{$_file_version->getCreatedOn()|ago nofilter} {lang}by{/lang} {user_link user=$_file_version->getCreatedBy()}</td>
	        <td class="options">
	          {if $_file_version->canDelete($_file_versions_user)}<a href="{$_file_version->getDeleteUrl()}" title="{lang}Delete Permanently{/lang}" class="remove_version"><img src="{image_url name="icons/12x12/delete.png" module=$smarty.const.ENVIRONMENT_FRAMEWORK}" alt="" /></a>{/if}
	        </td>
	      </tr>
	      {/foreach}
	    {/if}
      </tbody>
    </table>
  </div>

{if $_file_versions_wrap}
</div>

<script type="text/javascript">
  $('#{$_file_versions_id nofilter}').each(function() {
    var wrapper = $(this);

    var versions_table = wrapper.find('table.file_versions');
    var new_version_form_wrapper = wrapper.find('div.upload_new_file_version');
    var new_version_form = new_version_form_wrapper.find('form:first');
    var submit_button = new_version_form.find('button');
    var can_upload_new_version = {$_file_versions_file->canUploadNewVersions($_file_versions_user)|json nofilter}

    /**
     * Initialize row
     *
     * @param jQuery row
     */
    var init_row = function (row) {
      row.find('a.remove_version').asyncLink({
        confirmation : App.lang('Are you sure that you want to permanently remove this file version?'),
        indicator_url : App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-small.gif', 'environment'),
        success : function() {
          row.remove();
          App.Wireframe.Flash.success('File version successfully removed');
        },
        error : function() {
          App.Wireframe.Flash.error('Failed to remove selected file version. Please try again later');
        }
      });
    }; // init_row

    // initialize every row
    versions_table.find('tr').each(function() {
      init_row($(this));
    });

    // upload_form_toggling
    var upload_new_version_button = wrapper.find('a.upload_new_file_version');
    if (upload_new_version_button.length) {
      upload_new_version_button.click(function () {
        if (new_version_form_wrapper.is(':visible')) {
          new_version_form_wrapper.slideUp().find('input[type=file]').val('');
        } else {
          new_version_form_wrapper.slideDown(400, function () {
            new_version_uploader.refresh();
          });
        } // if
        return false;
      });
    } // if

    // cancel upload button
    var cancel_upload = wrapper.find('a.upload_new_file_version_cancel').click(function () {
      if (new_version_form.is('.uploading')) {
        return false;
      } // if

      end_upload();
      return false;
    });

    var file_upload_label = $('#new_file_version_uploader_wrapper .file_upload_label:first');
    var original_file_upload_label_text = file_upload_label.text();

    var upload_progressbar = $('#new_file_version_uploader_wrapper .upload_progressbar').hide();
    var upload_progressbar_bar = upload_progressbar.find('.upload_progressbar_bar');

    /**
     * start the upload
     */
    var start_upload = function () {
      new_version_form.addClass('uploading');
      submit_button.attr('disabled', 'disabled');
    }; // start_upload

    /**
     * End upload
     */
    var end_upload = function () {
      new_version_form_wrapper.slideUp();
      new_version_form.removeClass('uploading');
      file_upload_label.text(original_file_upload_label_text);
      submit_button.removeAttr('disabled');
      upload_progressbar.hide();
    }; // end_upload

    // construct the uploader
    var new_version_uploader = new plupload.Uploader({
      'url'                   : new_version_form.attr('action'),
      'container'             : 'new_file_version_uploader_wrapper',
      'browse_button'         : 'new_file_version_uploader_wrapper_button',
      'file_data_name'        : 'new_file_version',
      'runtimes'              : {$uploader_options.runtimes|json nofilter},
      'max_file_size'         : {$uploader_options.size_limit|json nofilter},
      'flash_swf_url'         : {$uploader_options.flash_uploader_url|json nofilter},
      'silverlight_xap_url'   : {$uploader_options.silverlight_uploader_url|json nofilter},
      'multi_selection'       : false,
      'multipart'             : true,
      'multipart_params'      : { 'submitted' : 'submitted' }
    });

    new_version_uploader.bind('FilesAdded', function (uploader, files) {
      file_upload_label.text(files[0]['name']);
    });

    new_version_uploader.bind('UploadProgress', function(uploader, file) {
      upload_progressbar_bar.css('width', file.percent + '%');
    });

    new_version_uploader.bind('Error', function (uploader, error) {
      end_upload();
      App.Wireframe.Flash.error(error.message);
    });

    new_version_uploader.bind('FileUploaded', function (uploader, file, response) {
      if ($.browser.msie) {
        var objects_list = versions_table.parents('.objects_list_wrapper:first');
        if (objects_list.length) {
          objects_list.objectsList('reload_current');
        } else {
          App.Wireframe.Content.reload();
        } // if

        return false;
      } // if

      {literal}var response_is_object = response.response.indexOf('{') === 0;{/literal}

      if (!response_is_object) {
        App.Wireframe.Flash.error(response.response);
        end_upload();
      } else {
        eval('var response = ' + response.response);
        response['body'] = (response['body'] + '').unclean();

        var table_body = versions_table.find('tbody');
        table_body.empty();

        var versions = response.versions;
        if (versions && versions.length) {
          $.each(versions, function (index, version) {
            var row = $('<tr class="file_version latest">' +
              '<td class="version"><a target="_blank" href="' + version.urls.view + '">' + App.lang('Version #:version_num', { version_num : version.version }) + '</a></td>' +
              '<td class="size">' + App.formatFileSize(version.size) + '</td>' +
              '<td class="details"><span title="' + version.created_on.formatted + '" class="ago">' + App.Wireframe.Utils.ago(version.created_on) + '</span> ' + App.lang('by') + ' <a class="user_link" href="' + version.created_by.permalink + '">' + App.clean(version.created_by.display_name) + '</a></td>' +
              '<td class="options"><a class="remove_version" title="Delete Permanently" href="' + version.urls['delete'] + '"><img alt="" src="' + App.Wireframe.Utils.imageUrl('/icons/12x12/delete.png', 'environment') + '"></a></td>' +
              '</tr>').appendTo(table_body);
            init_row(row);
          });
        } // if

        var latest_row = $('<tr class="file_version latest">' +
          '<td class="version"><a target="_blank" href="' + response.urls.download + '">' + App.lang('Latest Version') + '</a></td>' +
          '<td class="size">' + App.formatFileSize(response.size) + '</td>' +
          '<td class="details"><span title="' + response.last_version_on.formatted + '" class="ago">' + App.Wireframe.Utils.ago(response.last_version_on) + '</span> ' + App.lang('by') + ' <a class="user_link" href="' + response.last_version_by.permalink + '">' + App.clean(response.last_version_by.display_name) + '</a></td>' +
          '<td class="options"><span class="latest_file_version">' + App.lang('Latest') + '</span></td>' +
          '</tr>');

        latest_row.prependTo(table_body);

        // show success message
        App.Wireframe.Flash.success('New file version uploaded');

        // trigger event
        App.Wireframe.Events.trigger('new_file_version_created', response);
        App.Wireframe.Events.trigger('asset_updated', response);

        // hide form
        end_upload();
      } // if
    });

    // initialize uploader
    if (can_upload_new_version) {
      new_version_uploader.init();
    } // if

    // on form submission, start the uploader
    new_version_form.submit(function () {
      if (new_version_form.is('.uploading')) {
        return false;
      } // if

      upload_progressbar.show();
      new_version_uploader.start();
      start_upload();
      return false;
    });
  });
</script>
{/if}