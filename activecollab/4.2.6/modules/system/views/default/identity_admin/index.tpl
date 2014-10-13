{title}Identity{/title}
{add_bread_crumb}Identity Settings{/add_bread_crumb}
{use_widget name="form" module="environment"}

<div id="identity_admin">
  {form action=Router::assemble('identity_admin') method="post" enctype="multipart/form-data"}
    <div class="content_stack_wrapper">

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}General{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=identity_name}
          	{text_field name="settings[identity_name]" value=$settings_data.identity_name label='System Name'}
          	<p class="aid">{lang}Name your project collaboration system. This name will be used as prefix for title of all pages, as well as Welcome home screen widget{/lang}</p>
          {/wrap}

          {wrap field=rep_site_domain}
            {text_field name="settings[rep_site_domain]" value=$settings_data.rep_site_domain label='Default Rep Site Domain'}
            <p class="aid">{lang}Repsite Domain Name eg: abuckagallon.com{/lang}</p>
          {/wrap}
          
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Welcome Message{/lang}</h3>
          <p class="aid">{lang}Set up welcome message that is displayed to your clients on their Dashboard page{/lang}</p>
        </div>
        <div class="content_stack_element_body">
          {wrap field=identity_client_welcome_message}
            {textarea_field name="settings[identity_client_welcome_message]" id='identity_admin_welcome_message' label='Welcome Message'}{$settings_data.identity_client_welcome_message nofilter}{/textarea_field}
            <p class="aid">{lang}Welcome message that is displayed to your clients{/lang}. {lang}New lines will be preserved. HTML is not allowed{/lang}.</p>
          {/wrap}

          {wrap field=identity_nidentity_logo_on_whiteame}
            {yes_no name="settings[identity_logo_on_white]" value=$settings_data.identity_logo_on_white label='Put Our Logo on the White Background'}
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}System Logo{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=identity_logo}
            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="{$large_logo_url}" alt="{lang}Logo{/lang}" original_image="{$large_logo_url}">
                </td>
                <td class="logo_input">
                  <input type="file" name="logo" /><br /><a href="{$revert_logo_url}" class="revert_link">{lang}Revert to default image{/lang}</a>
                </td>
              </tr>
            </table>
            <p class="aid">{lang small_logo_url=$small_logo_url medium_logo_url=$medium_logo_url large_logo_url=$large_logo_url larger_logo_url=$larger_logo_url photo_logo_url=$photo_logo_url}This logo is used in email notifications, welcome messages and more. It is saved in four sizes: <a href=":small_logo_url" target="_blank">16x16px</a>, <a href=":medium_logo_url" target="_blank">40x40px</a>, <a href=":large_logo_url" target="_blank">80x80px</a>, <a href=":larger_logo_url" target="_blank">128x128</a> and <a href=":photo_logo_url" target="_blank">256x256px</a>. System uses different sizes for different purposes, depending on the need{/lang}</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Login Page Logo{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=login_page_logo}
            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="{$login_page_logo}" alt="{lang}Logo{/lang}" original_image="{$login_page_logo}">
                </td>
                <td class="logo_input">
                  <input type="file" name="login_page_logo" /><br /><a href="{$revert_login_logo_url}" class="revert_link">{lang}Revert to default image{/lang}</a>
                </td>
              </tr>
            </table>
            <p class="aid">{lang}This logo will be used only on login and reset password pages. We recommend transparent PNG file with dimensions 256x256px. If image is not that size it will be constrained to those dimensions.{/lang}</p>
          {/wrap}
        </div>
      </div>

      <div class="content_stack_element">
        <div class="content_stack_element_info">
          <h3>{lang}Favicon{/lang}</h3>
        </div>
        <div class="content_stack_element_body">
          {wrap field=favicon}
            <table cellspacing="0" class="logo_table">
              <tr>
                <td class="logo_cell">
                  <img src="{$favicon_url}" alt="{lang}Logo{/lang}" original_image="{$favicon_url}">
                </td>
                <td class="logo_input">
                  <input type="file" name="favicon" /><br /><a href="{$revert_favicon_url}" class="revert_link">{lang}Revert to default image{/lang}</a>
                </td>
              </tr>
            </table>
            <p class="aid">{lang}Image has to be 16x16px, and file type has to be <strong>ICO</strong>.{/lang}</p>
          {/wrap}
        </div>
      </div>

    </div>
    
    {wrap_buttons}
  	  {submit}Save Changes{/submit}
    {/wrap_buttons}
  {/form}
</div>

<script type="text/javascript">
  var wrapper = $('#identity_admin');

  wrapper.find('.revert_link').click(function () {
    var anchor = $(this);
    var image = anchor.parents('table:first').find('td:first img');

    if (anchor.is('.in_progress')) {
      return false;
    } // if

    anchor.addClass('in_progress').html(App.lang('Reverting') + ' ...');

    $.ajax({
      'url'     : App.extendUrl(anchor.attr('href')),
      'type'    : 'post',
      'data'    : { 'submitted' : 'submitted' },
      'complete'  : function () {
        anchor.html(App.lang('Revert to default image')).removeClass('in_progress');
      },
      'success' : function (success) {
        image.attr('src', App.extendUrl(image.attr('src'), { 'timestamp' :$.now() }));
      }
    });

    return false;
  });
</script>