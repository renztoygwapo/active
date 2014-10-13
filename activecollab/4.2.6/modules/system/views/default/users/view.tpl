{add_bread_crumb}Profile{/add_bread_crumb}

{object object=$active_user user=$logged_user}
  <div class="wireframe_content_wrapper">
    {inline_tabs object=$active_user}
  </div>
{/object}

{if !empty($personality_type)}
  {select_personality_type value=$personality_type id="personality_type_hidden" render_type="hidden"}
{/if}


<script type="text/javascript">
  
  $(document).ready(function(){
    var personality_type = $('#personality_type_hidden').val();
    if(typeof personality_type_hidden != 'undefined') {
      var _html =  "<div class='property'><div class='label'>Personality Type</div><div class='content'>"+personality_type +"</div></div>";
      $('.vcard_data .properties').append(_html);
    }
  });

  App.Wireframe.Events.bind('user_updated.{$request->getEventScope()}', function (event, user) {
    if(user['class'] == 'User' && user.id == '{$active_user->getId()}') {
    	// update avatar on the profile page
      $('#user_page_' + user.id + ' #select_user_icon .properties_icon').attr('src', user.avatar.photo);

      // if user is changing their own avatar, update the image at the bottom left corner
      if ('{$active_user->getId()}' == '{$logged_user->getId()}') {
        $('#menu_item_profile img').attr('src', user.avatar.large);
        $('#menu_item_profile span.label').html(user.display_name);
      } // if
    } // if
  });
</script>