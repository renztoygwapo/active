<div id="hg_settings">
{form action="$settings_source_url" method="post" id="main_form"}
  <div class="content_stack_wrapper">
    <div class="content_stack_element">
      <div class="content_stack_element_info">
        <h3>{lang}General{/lang}</h3>
      </div>
      <div class="content_stack_element_body">
        {wrap field=mercurial_path}
          {text_field id='mercurial_path' name="source[mercurial_path]" value=$source_data.mercurial_path label='Mercurial Location'}
          <p class="details">{lang}Enter path for Mercurial executable (without executable name){/lang}</p>
        {/wrap}

        <div class="admin_test_setting" id="check_mercurial_path">
          <button type="button"><span><span>{lang}Check Mercurial Location{/lang}</span></span></button>
          <span class="test_results">
            <span></span>
          </span>
        </div>
      </div>
    </div>
  </div>

  {wrap_buttons}
    {submit}Save Changes{/submit}
  {/wrap_buttons}
{/form}
</div>
<input type="hidden" value="{$test_mercurial_url}" id="test_mercurial_url" />

<script type="text/javascript">
  $('#hg_settings').each(function() {
    var test_results_div = $(this).find('.test_results');
    var test_div = test_results_div.parent();

    test_results_div.prepend('<img class="source_results_img" src="" alt=""/>');
    $('.source_results_img').hide();

    // AJAX call for testing mercurial path
    $('#check_mercurial_path button').click(function () {
      $('.source_results_img').show();
      var mercurial_path = $('#mercurial_path').val();
      var indicator_img = $('.source_results_img');
      var result_span = test_div.find('.test_results span:eq(0)');
      indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl());
      result_span.html('');
      var test_url = App.extendUrl($('#test_mercurial_url').val(), {
        'async' : 1,
        'mercurial_path' : mercurial_path
      });
      $.get(test_url, function(data) {
        if ($.trim(data)=='true') {
          indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl('ok'));
          result_span.html(App.lang('Mercurial executable found'));
        } else {
          indicator_img.attr('src', App.Wireframe.Utils.indicatorUrl('error'));
          result_span.html(App.lang('Error accessing Mercurial executable') + ': ' + data);
        } // if
      });
    });
  });
</script>
