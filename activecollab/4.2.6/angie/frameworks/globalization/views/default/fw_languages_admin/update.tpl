{title}Update Language{/title}
{add_bread_crumb}Upload XML{/add_bread_crumb}
{use_widget name="form" module="environment"}

<div class="page_wrapper">
  {if $update_enabled}
    {form method=post action=$update_url id="update_form" enctype="multipart/form-data"}
      {wrap field=xml}
        {label for=xml}Select Language XML File{/label}
        {file_field name=xml id=xml}
      {/wrap}

      {button type="submit" id="update_btn"}Update{/button}
    {/form}
    <div id="update_language"></div>
  {else}
    <p>{lang}Importing is not enabled, please review errors{/lang}</p>
  {/if}
</div>

<script type="text/javascript">
  var form = $("#update_form");
  form.find('button').click(function() {
    var file_input = form.find('input[type=file]:first');

    if (!file_input.val()) {
      App.Wireframe.Flash.error('Please choose XML file which contains language translation');
      return false;
    } // if

    if (!confirm(App.lang('Overwrite current translations with uploaded XML file translations?'))) {
      return false;
    } //if

    form.ajaxSubmit({
      'url' : App.extendUrl(form.attr('action'), {
        'update' : 1,
        'async' : 1
      }),
      'type' : 'post',
      'success' : function(response) {
        form.hide();
        file_input.val('');
        $('#update_language').html(response);
        init_update_form();
      },
      'error' : function(response) {
        App.Wireframe.Flash.error(App.lang('An error occurred while trying to update language'));
        form.show();
      }
    });

    return false;
  }); //updating language

  var init_update_form = function() {
    try {
      $('#initialize_update_process').asyncProcess({
        'success' : function(response, step_num) {
          $('#initialize_update_process').after(response);
          App.widgets.FlyoutDialog.front().close();
          App.Wireframe.Content.reload();
          App.Wireframe.Events.trigger('language_updated',response);
        },
        'error' : function (response) {
          $('#update_language #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();
          form.show();
        },
        'on_step_success' : function (response) {
        },
        'on_step_error' : function (response) {
          $('#update_language #message').html(App.Wireframe.Utils.responseToErrorMessage(response)).css('color','red').show();
          form.show();
        }
      });
    } catch (Exception) {

    } // try
  }; //init_update_form
</script>