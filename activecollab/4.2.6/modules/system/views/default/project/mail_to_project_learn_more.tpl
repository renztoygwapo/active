{title}Mail to Project{/title}
{use_widget name=mail_to_project_learn_more module=$smarty.const.SYSTEM_MODULE}

<div id="mail_to_project_learn_more">
  <p>{lang}Using this feature, you can simply send emails to this project, and the system will automatically convert them into tasks, discussions, files and text documents. Please note that <u>every project has its own email address</u>!{/lang}</p>
  <p>{lang}The rules that system follows when converting your messages into project elements are described below{/lang}:</p>

  <div class="mail_to_project_options">
    <ul>
      <li class="selected" whats_selected="task"><img src="{image_url name='mail-to-project/email-task.png' module=$smarty.const.SYSTEM_MODULE}"> <span>{lang}Email Tasks{/lang}</span></li>
      <li whats_selected="discussion"><img src="{image_url name='mail-to-project/email-discussion.png' module=$smarty.const.SYSTEM_MODULE}"> <span>{lang}Email Discussions{/lang}</span></li>
      <li whats_selected="files"><img src="{image_url name='mail-to-project/email-files.png' module=$smarty.const.SYSTEM_MODULE}"> <span>{lang}Email Files{/lang}</span></li>
      <li whats_selected="forward"><img src="{image_url name='mail-to-project/email-forward.png' module=$smarty.const.SYSTEM_MODULE}"> <span>{lang}Forward Messages{/lang}</span></li>
    </ul>
  </div>

  <div class="mail_to_project_email_placeholder">
    <div class="mail_to_project_email_element mail_to_project_email_to">{mailto address=$active_project->getMailToProjectEmail()}</div>
    <div class="mail_to_project_email_element mail_to_project_email_subject">Task: {lang}Enter task summary as email subject{/lang}</div>
    <div class="mail_to_project_email_element mail_to_project_email_body">
      <div class="mail_to_project_email_body_text">
        {lang}Provide detailed task description in email body{/lang}.<br><br>{lang type=lang('task')}Attachments will be also imported and attached to the :type!{/lang}
      </div>

      <div class="mail_to_project_email_body_attachments">
        <p>{lang}Attachments{/lang}:</p>

        <table class="auto" cellspacing="0">
          <tr>
            <td class="icon"><img src="{image_url name='mail-to-project/attachment-1.png' module=$smarty.const.SYSTEM_MODULE}"></td>
            <td class="name">image.png</td>

            <td class="icon"><img src="{image_url name='mail-to-project/attachment-2.png' module=$smarty.const.SYSTEM_MODULE}"></td>
            <td class="name">archive.png</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  $('#mail_to_project_learn_more').each(function() {
    var wrapper = $(this);
    var list = wrapper.find('div.mail_to_project_options ul');

    var total_elements = 0;
    var total_width = 0;

    wrapper.find('div.mail_to_project_options ul li').each(function() {
      total_elements++;
      total_width += $(this).width();
    });

    list.width(total_width + (total_elements * 12) + 2 + 'px');

    //var email_to = wrapper.find('div.mail_to_project_email_to');
    var email_subject = wrapper.find('div.mail_to_project_email_subject');
    var email_body_text = wrapper.find('div.mail_to_project_email_body_text');
    var email_body_attachments = wrapper.find('table.mail_to_project_email_body_attachments');

    /**
     * Update email preview when selection changes
     *
     * @param String whats_selected
     */
    var update_email_preview = function(whats_selected) {
      switch(whats_selected) {
        case 'task':
          email_subject.text('Task: ' + App.lang('This text will become task summary'));
          email_body_text.html(App.lang('Provide detailed task description in the email body') + '. <br><br>' + App.lang('Attachments will be also imported and attached to the :type!', {
            'type' : App.lang('task')
          }));
          email_body_attachments.show();
          break;
        case 'discussion':
          email_subject.text('Discussion: ' + App.lang('This text will become discussion topic'));
          email_body_text.html(App.lang('Text of your email message will be set as detailed discussion description') + '. <br><br>' + App.lang('Attachments will be also imported and attached to the :type!', {
            'type' : App.lang('discussion')
          }));
          email_body_attachments.show();
          break;
        case 'files':
          email_subject.text('Files');
          email_body_text.html(App.lang('All files attached to this message will be imported as individual files under Files tab. Text that you provide in email body will be set as description for all of the files') + '.');
          email_body_attachments.show();
          break;
        case 'forward':
          email_subject.text('FW Subject --OR-- Fw: Subject --OR-- Document: Subject');
          email_body_text.html(App.lang('Simply forward any messages and system will import it as a Text Document in Files section') + '. <br><br>' + App.lang('Attachments will be also imported and attached to the :type!', {
            'type' : App.lang('text document')
          }));
          email_body_attachments.show();
          break;
      } // switch
    }; // update_email_preview

    list.on('click', 'li', function(e) {
      var list_item = $(this);

      if(!list_item.is(':selected')) {
        list.find('li').removeClass('selected');
        list_item.addClass('selected');

        update_email_preview(list_item.attr('whats_selected'));
      } // if
    });
  })
</script>