{if $update_steps}

  <div id="update_steps">
    <ul id="initialize_update_process" class="async_process">
      {foreach $update_steps as $update_step}
        <li class="step" step_url="{$update_step.url}">{$update_step.text}</li>
      {/foreach}
    </ul>
  </div>

  <p style="display: none; margin-left:10px;" id="message"></p>


{/if}