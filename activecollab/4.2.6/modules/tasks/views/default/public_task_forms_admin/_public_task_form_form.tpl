{use_widget name="select_users_inline" module="authentication"}
{use_widget name="select_subscribers" module="subscriptions"}

<script type="text/javascript">
  App.widgets.FlyoutDialog.front().setAutoSize(false);
</script>

<div class="big_form_wrapper two_form_sidebars public_task_form">
  <div class="main_form_column">
    {wrap field=name}
      {text_field name="public_task_form[name]" value=$public_task_form_data.name class='title' required=true label="Page Title"}
    {/wrap}
    
    {wrap_editor field=body}
      {label}Description{/label}
      {editor_field name="public_task_form[body]"}{$public_task_form_data.body nofilter}{/editor_field}
    {/wrap_editor}
  </div>
  
  <div class="form_sidebar form_first_sidebar">
    {wrap field=slug}
      {text_field name="public_task_form[slug]" value=$public_task_form_data.slug label="Short Name" required=true}
    {/wrap}
    
    {wrap field=project_id}
      {select_project name="public_task_form[project_id]" value=$public_task_form_data.project_id label="Target Project" optional=false show_all=true user=$logged_user id="active_project"}
    {/wrap}
    
    {wrap field="sharing"}
      {label}Sharing{/label}
      {select_public_form_sharing_type name="public_task_form[sharing]" value=$public_task_form_data.sharing expiration_name="public_task_form[expire_after]" expiration_value=$public_task_form_data.expire_after}
      
      <div class="sharing_notification">
	      <div>{checkbox_field name="public_task_form[subscribe_author]" checked=$public_task_form_data.subscribe_author value="1" label="Notify Author on Tasks Updates" id="subscribe_author"}</div>
      </div>
      
      <div class="comment_options">
        <div>{checkbox_field name="public_task_form[comments_enabled]" checked=$public_task_form_data.comments_enabled label="Enable Comments" value="1" id="enable_comments"}</div>
        <div>{checkbox_field name="public_task_form[reopen_on_comment]" checked=$public_task_form_data.reopen_on_comment label="Reopen on new Comment" value="1" id="reopen_on_comment"}</div>      
      </div>

      <div class="attachment_options">
        <div>{checkbox_field name="public_task_form[attachments_enabled]" checked=$public_task_form_data.attachments_enabled label="Allow file upload" value="1" id="attachments_enabled"}</div>
      </div>
    {/wrap}
  </div>
  
  <div class="form_sidebar form_second_sidebar">
    {wrap field=notify_users}
      {label}Choose Subscribers{/label}
      <div class="subscribers">
        {lang}Choose Target project first{/lang}
      </div>
    {/wrap}
  </div>
</div>

<script type="text/javascript">
  $('.public_task_form:first').each(function() {
    var wrapper = $('.public_task_form:first');
    var user_list_url = '{assemble route="public_task_form_subscribers" project_id="--PROJECT-ID--"}';
    var initial_subscribers = {$subscribers|json nofilter};
  
    // current project
    var current_project = wrapper.find('#active_project');
    var subscribers_wrapper = wrapper.find('.form_second_sidebar div.subscribers');
    current_project.change(function () {
      var project_id = current_project.val();

      if(project_id) {
        var url = user_list_url.replace('--PROJECT-ID--', project_id);
        subscribers_wrapper.empty().addClass('loading').css('background-image', 'url(' + App.Wireframe.Utils.indicatorUrl() + ')');

        $.ajax({
          'url' : url,
          'data' : {
            'subscribers' : initial_subscribers
          },
          'type' : 'post',
          'success' : function (response) {
            subscribers_wrapper.append(response).removeClass('loading').css('background-image', 'none');
          },
          'error' : function (response) {
            subscribers_wrapper.append(App.lang("Couldn't load list of users")).removeClass('loading').css('background-image', 'none');
          }
        });
      } else {
        subscribers_wrapper.empty().append(App.lang('Choose target project first'));
      } // if
    });
    current_project.trigger('change');
  
    // comments
    var enable_comments = wrapper.find('#enable_comments');
    var reopen_on_comment = wrapper.find('#reopen_on_comment');
    enable_comments.change(function () {
      if (enable_comments.is(':checked')) {
        reopen_on_comment.parent().show();
      } else {
        reopen_on_comment.parent().hide();
      } // if
    });
    enable_comments.trigger('change');
  });
</script>
