{title}{$active_public_task_form->getName()}{/title}

{if $active_public_task_form->hasBody()}
<div class="public_form_description">
  {$active_public_task_form->getBody() nofilter}
</div>
{/if}

<div class="public_form">
  {form method="post" enctype="multipart/form-data" action=$active_public_task_form->getPublicUrl()}
    {if $user instanceof IUser && (!$user instanceof AnonymousUser)}
      <div class="meta">
        {user_link user=$user}
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

    {if $uploads_enabled}
      {wrap field=attachment}
        {file label="Attachment"}
      {/wrap}
    {/if}
    
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