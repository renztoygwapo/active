{title}Videos{/title}
{add_bread_crumb}Videos{/add_bread_crumb}
{use_widget name=jwplayer module=$smarty.const.ENVIRONMENT_FRAMEWORK}

<div id="help_videos_wrapper">
  <div id="help_videos">
    <div id="wrap_help_video_player">
      <div id="help_video_player">{image name='chalkboard.png' modifiers='2x' module=$smarty.const.HELP_FRAMEWORK}</div>
    </div>

    <div id="help_video_groups">
      {foreach $video_groups as $video_group_name => $video_group}
      <div class="help_video_group">
        <h3>{$video_group}</h3>
        <ul>
          {foreach $videos as $video}
            {if $video->getGroupName() == $video_group_name}
              <li data-source-url="{$video->getSourceUrl()}" data-source-high-res-url="{$video->getSourceUrl('2X')}" {if $selected_video && $video->getShortName() == $selected_video} class="autoplay"{/if}>{$video->getTitle()}</li>
            {/if}
          {/foreach}
        </ul>
      </div>
      {/foreach}
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#help_videos_wrapper').each(function() {
    var wrapper = $(this);

    var player = false;
    var player_url = '{$player_url}';

    wrapper.on('click', '#help_videos div.help_video_group ul li', function() {
      var list_item = $(this);

      if(list_item.is('.playing')) {
        if(list_item.is('.paused')) {
          list_item.removeClass('paused');
          jwplayer('help_video_player').play();
        } else {
          list_item.addClass('paused');
          jwplayer('help_video_player').pause();
        } // if

        return;
      } // if

      wrapper.find('#help_videos div.help_video_group ul li').removeClass('playing');

      list_item.addClass('playing');

      if(player === false) {
        player = wrapper.find('#help_video_player').height('400px').width('600px');
      } // if

      var player_settings = {
        'file' : list_item.data('sourceUrl'),
        'flashplayer' : player_url,
        'height' : 360,
        'width' : 640,
        'events' : {
          'onReady' : function() {
            this.play();
          }
        }
      };

      var source_url = list_item.data('sourceUrl');

//      @TODO: Uncomment this to enable 720p videos

      var source_high_res_url = list_item.data('sourceHighResUrl');

      if(source_high_res_url) {
        player_settings['levels'] = [
          { bitrate: 500, file: source_url, width: 360 },
          { bitrate: 2000, file: source_high_res_url, width: 720 }
        ];
      } else {
        player_settings['file'] = source_url;
      } // if

      player_settings['file'] = source_url;

      jwplayer('help_video_player').setup(player_settings);
    });

    var selected_video = wrapper.find('li.autoplay:first');
    if(selected_video.length > 0) {
      selected_video.removeClass('autoplay').click();
    } // if

    // when we click the image placeholder, play the first item in the list
    $('#help_video_player img').click(function () {
      $('#help_video_groups .help_video_group:first ul:first li:first').click();
      return false;
    });
  });
</script>