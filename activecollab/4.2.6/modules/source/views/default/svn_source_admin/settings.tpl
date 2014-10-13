<div id="svn_settings">
  {form action="$settings_source_url" method="post" id="main_form"}
    <div class="content_stack_wrapper">
      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Subversion Interface{/lang}</h3>
          <p class="aid">{lang}Select which interface activeCollab will use to communicate with SVN repositories{/lang}</p>
        </div>
        <div class="content_stack_element_body">
          <div id="radio_group">
            <label for="radio_svn_extension">
              <input id="radio_svn_extension" type="radio" name="source[svn_type]" value="extension"
                {if !$svn_extension} disabled="disabled"
                {elseif $source_data.svn_type === "extension"} checked="checked" {/if}
              />
              {lang}SVN Extension{/lang}
            </label>
            {if !$svn_extension}
              <div class="slide_down_settings">
                <p class="details">{lang}This method requires SVN PHP extension to be installed. Read more about SVN extension in PHP documentation{/lang}: <a href="http://www.php.net/manual/en/book.svn.php">http://www.php.net/manual/en/book.svn.php</a></p>
              </div>
            {/if}

            <label for="radio_svn_exec">
              <input id="radio_svn_exec" type="radio" name="source[svn_type]" value="exec"
                {if !$svn_exec_path} disabled="disabled"
                {elseif $source_data.svn_type === "exec"} checked="checked" {/if}
              />
              {lang}SVN Over Executable Command{/lang}
            <label for="radio_svn_exec">
            {if !$svn_exec_path}
              <p class="details">{lang}This method requires XML PHP extension to be installed. Read more about XML extension in PHP documentation:
              <a href="http://www.php.net/xml">http://www.php.net/xml</a>{/lang}</p>
            {/if}
          </div>

          <div id="svn_exec" class="slide_down_settings">
            <p id="svn_exec_deprecated"><b>{lang}DEPRECATED{/lang}</b>: {lang svn_extension_url='http://www.php.net/manual/en/book.svn.php'}Please note that SVN over Executable Command has been deprecated as of activeCollab 2.3.10. This interface will be <u>completely removed in activeCollab 4</u>. Please use <a href=":svn_extension_url">SVN extension</a> instead{/lang}.</p>

            {wrap field=svn_path}
              {text_field id='svn_path' name="source[svn_path]" value=$source_data.svn_path label='SVN Location'}
              <p class="details">Enter path for SVN executable (without executable name)</p>
            {/wrap}

            <div class="admin_test_setting" id="check_svn_path">
              <button type="button"><span><span>{lang}Check SVN Location{/lang}</span></span></button>

              <span class="test_results">
                <span></span>
              </span>
            </div>

            {wrap field=svn_config_dir}
              {text_field id="svn_config_dir" name="source[svn_config_dir]" value=$source_data.svn_config_dir label='SVN Config Directory Path'}
              <p class="details">{lang}Leave empty to use system default{/lang}</p>
            {/wrap}

            {wrap field=svn_trust_server_cert}
              {yes_no name="source[svn_trust_server_cert]" label='SVN Trust Server Certificate?' value=$source_data.svn_trust_server_cert}
              <p class="details">{lang}This option is valid only for Subversion v1.6 and later{/lang}</p>
            {/wrap}
          </div>
        </div>
      </div>
    </div>

    {wrap_buttons}
      {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>
<input type="hidden" value="{$test_svn_url}" id="test_svn_url" />

<script type="text/javascript">
  $('#svn_settings').each(function() {
    var test_results_div = $(this).find('.test_results');
    var test_div = test_results_div.parent();
    var radio_extension = $('#radio_svn_extension');
    var radio_exec = $('#radio_svn_exec');

    if (! radio_exec.prop("checked")) {
      $('#svn_exec').hide();
    } // if

    test_results_div.prepend('<img class="source_results_img" src="" alt=""/>');
    $('.source_results_img').hide();

    radio_extension.change(function () {
      $('#svn_exec').hide('fast');
    });

    radio_exec.change(function () {
      $('#svn_exec').show('fast');
    });

    // AJAX call for testing svn path
    $('#check_svn_path button').click(function () {
      $('.source_results_img').show();
      var svn_path = $('#svn_path').val();
      var svn_config_dir = $('#svn_config_dir').val();
      var indicator_img = $('.source_results_img');
      var result_span = test_div.find('.test_results span:eq(0)');
      indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl());
      result_span.html('');
      var test_url = App.extendUrl($('#test_svn_url').val(), {
        'async' : 1,
        'svn_path' : svn_path,
        'svn_config_dir' : svn_config_dir
      });
      $.get(test_url, function(data) {
        if ($.trim(data)=='true') {
          indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl('ok'));
          result_span.html(App.lang('SVN executable found'));
        } else {
          indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl('error'));
          result_span.html(App.lang('Error accessing SVN executable') + ': ' + data);
        } // if
      });
    })
  });
</script>
