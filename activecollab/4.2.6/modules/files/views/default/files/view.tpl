{add_bread_crumb}Details{/add_bread_crumb}
{use_widget name=jwplayer module=$smarty.const.ENVIRONMENT_FRAMEWORK}

{object object=$active_asset user=$logged_user show_body=false}
  <div class="wireframe_content_wrapper">
    
    <div class="project_asset_file_preview">
      <div class="real_preview">
        {$active_asset->preview()->renderLarge() nofilter}
      </div>
      
	    <div class="object_body_content formatted_content">
	      {if $active_asset->inspector()->hasBody()}
	        {$active_asset->inspector()->getBody() nofilter}
	      {/if}
	    </div>
    </div>
    
  </div>
  
  <div class="wireframe_content_wrapper">{file_versions file=$active_asset user=$logged_user id="file_versions_for_{$active_asset->getId()}"}</div>
  <div class="wireframe_content_wrapper">{object_comments object=$active_asset user=$logged_user show_first=yes}</div>
{/object}

<script type="text/javascript">
  App.Wireframe.Events.bind('new_file_version_created.{$request->getEventScope()}', function (event, file) {
    var current_preview = $('.project_asset_file_preview .real_preview');
    var previous_height = current_preview.height();
    var previus_content = current_preview.children().hide();

    previus_content.hide();
    current_preview.css({
      'background-image' : 'url(' + App.Wireframe.Utils.imageUrl('layout/bits/indicator-loading-big.gif', 'environment') + ')',
      'height' : previous_height 
    });

    $.ajax({
       url : App.extendUrl(file.urls.preview, {
         'skip_layout' : 1
       }),
       success : function (response) {
         current_preview.empty().append(response).css({
           'background-image' : 'none',
           'height' : 'auto'
         });
       },
       error : function (response) {
         previus_content.show();
         current_preview.css({
           'background-image' : 'none',
           'height' : 'auto'
         });
         App.Wireframe.Flash.error(App.lang('Failed to retrieve file preview'));
       }
    });
  });
</script>