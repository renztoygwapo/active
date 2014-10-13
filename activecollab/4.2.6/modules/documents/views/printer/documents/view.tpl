<div id="print_container">
{object object=$active_document user=$logged_user}
<div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_document->inspector()->hasBody()}
          {$active_document->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
      {if $active_document->preview()->has()}
      	{$active_document->preview()->renderLarge() nofilter}
      {else}
      	{lang}Preview not available.{/lang}
      {/if}
      </div>
      
    </div>
  </div>

{/object}
</div>