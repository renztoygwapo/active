{title title=$active_object->avatar()->getAvatarLabelName()}Update :title{/title}
{add_bread_crumb}View{/add_bread_crumb}
{use_widget name="avatar_dialog" module="avatar"}

{if $gd_library_loaded}
  <div class="fw_avatar_container" id="{$widget_id}">
    <table class="fw_avatar_table">
      <tr>
        <td class="fw_current_avatar_image left_container">
          <img src="{$current_avatar}" alt="{lang}Current Avatar{/lang}" class="current_avatar"/>
        </td>
        <td class="fw_avatar_actions right_container">
          <ul class="action_list">
            <li>
              <a href="{$active_object->avatar()->getUploadUrl()}" class="fw_avatar_action_upload_new_picture">{lang}Upload New Picture{/lang}</a>
              <form action="{$active_object->avatar()->getUploadUrl()}" method="post" class="hidden_upload_form" enctype="multipart/form-data">
                <input type="file" name="avatar">
              </form>
            </li>
            {if $active_object->avatar()->resize_mode == 'crop'}
              <li><a href="{$active_object->avatar()->getEditUrl()}" class="fw_avatar_action_crop_picture">{lang}Crop Picture{/lang}</a></li>
            {/if}
            <li><a href="{$active_object->avatar()->getRemoveUrl()}" class="fw_avatar_action_reset_to_default_picture">{lang}Reset to Default Picture{/lang}</a></li>
          </ul>
        </td>
      </tr>
    </table>

    <div class="fw_crop_widget">
      <div class="fw_crop_widget_wrapper">
      </div>

      <p>{lang}Select part of the image you want to use as avatar{/lang}</p>
      <div class="fw_crop_buttons">
        {button}Save{/button} {lang}or{/lang} <a href="#">{lang}Cancel{/lang}</a>
      </div>
    </div>

  </div>

  <script type="text/javascript">
    App.widgets.AvatarDialog.init({$widget_id|json nofilter}, {
      default_avatar : {$default_avatar|json nofilter},
      original_avatar : {$original_url|json nofilter},
      event_name : {$event_name|json nofilter},
      is_default : {if $default_avatar == $current_avatar}1{else}0{/if}
    });
  </script>
{else}
  <p class="empty_page">{lang}<a href="http://ch2.php.net/manual/en/book.image.php" target="_blank">GD library</a> that is needed for resizing images is not installed{/lang}.<br />{lang}Please contact your web server administrator{/lang}.</p>
{/if}