{title name=$active_object_parent->getName()}Attachments preview for ":name" {/title}
{use_widget name="gallery_view" module="preview"}

{if !$request->isQuickViewCall()}
  {$active_object->preview()->renderPreview($preview_width, $preview_height, true) nofilter}
{else}
  {assign var=gallery_id value=HTML::uniqueId('gallery_view')}
  <div id="{$gallery_id}"></div>

  <script type="text/javascript">
    $('#{$gallery_id}').galleryView({
      'items'         : {$items|json nofilter},
      'current_item'  : {$current_item|json nofilter},
      'event_scope'   : {$request->getEventScope()|json nofilter}
    });
  </script>
{/if}