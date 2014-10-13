{title}{$active_public_task_form->getName()}{/title}

{if $active_public_task_form->hasBody()}
  <div id="public_form_description" class="wireframe_content_wrapper">
    <div class="object_body_content">
      {$active_public_task_form->getBody() nofilter}
    </div>
  </div>
{/if}

<div id="public_form">
  {form method="post" enctype="multipart/form-data" action=$active_public_task_form->getPublicUrl()}
    {if $user instanceof IUser && (!$user instanceof AnonymousUser)}
      <div class="meta">
        <input type="hidden" name="task[created_by_name]" value="{$user->getDisplayName()}" />
        <input type="hidden" name="task[created_by_email]" value="{$user->getEmail()}" />
      </div>
    {else}
      {wrap field=created_by_name}
        {text_field name="task[created_by_name]" label="Your Name" value=$task_data.created_by_name required=true}
      {/wrap}

      {wrap field=created_by_email}
        {text_field name="task[created_by_email]" label="Your Email Address" value=$task_data.created_by_email required=true}
      {/wrap}
    {/if}

    {wrap field="title"}
      {text_field name="task[name]" label="Request Title" value=$task_data.name required=true}
    {/wrap}
    
    {wrap field="body"}
      {textarea_field name="task[body]" label="Your Comment" required=true}{$task_data.body nofilter}{/textarea_field}
    {/wrap}
    
    {if $captcha_enabled}
      {wrap field=captcha}
        {captcha name='task[captcha]' value=$task_data.captcha id=captcha required=yes label="Type the code shown"}
      {/wrap}
    {/if}
    
    {wrap_buttons}
      {submit}Submit Request{/submit}
    {/wrap_buttons}
  {/form}
</div>