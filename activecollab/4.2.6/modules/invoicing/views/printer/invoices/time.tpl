{title}Invoice related items{/title}

<div id="print_container">
{object object=$active_invoice user=$logged_user}
  <div class="wireframe_content_wrapper">
    <div class="object_body with_shadow">
      <div class="object_content_wrapper"><div class="object_body_content formatted_content">
        {if $active_invoice->inspector()->hasBody()}
          {$active_invoice->inspector()->getBody() nofilter}
        {else}
          {lang}No description provided{/lang}
        {/if}
      </div>
 		</div>
    </div>
  </div>
	<div class="wireframe_content_wrapper">{render_invoice_time_expense invoice=$active_invoice}</div>
  <div class="wireframe_content_wrapper">{object_history object=$active_invoice user=$logged_user}</div>
{/object}
</div>