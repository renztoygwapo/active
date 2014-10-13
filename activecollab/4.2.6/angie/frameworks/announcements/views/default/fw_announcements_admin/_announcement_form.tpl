{wrap field=subject}
  {text_field name="announcement[subject]" value=$announcement_data.subject label="Subject" id="subject" required=true}
{/wrap}

{wrap field=body}
  {textarea_field name='announcement[body]' label="Text" required=true}{$announcement_data.body nofilter}{/textarea_field}
{/wrap}

{wrap field=body_type}
  <div>{radio_field name='announcement[body_type]' value=0 pre_selected_value=$announcement_data.body_type label='Plain Text (newlines will be preserved, links will be clickable)'}</div>
  <div>{radio_field name='announcement[body_type]' value=1 pre_selected_value=$announcement_data.body_type label='Allow Basic HTML elements (a, b, i, u, table etc.)'}</div>
{/wrap}

{wrap field=icon}
  {select_announcement_icon name='announcement[icon]' value=$announcement_data.icon inline=true label="Icon" id="select_icon"}
{/wrap}

{wrap field=show_to}
  {label}Show To{/label}
  {select_show_to name='announcement[show_to]' value=$announcement_data.show_to user=$logged_user}

  {if !$active_announcement->isNew()}
    <p class="details">{lang}Note: Dismissed By counter will reset by updating this option{/lang}</p>
  {/if}
{/wrap}

{wrap field=expires_on}
  {label}Expires{/label}
  {select_expires_on name='announcement[expiration]' value=$announcement_data.expiration}
{/wrap}

{wrap field=notify_via_email}
  {label}Email Notifications{/label}
  {checkbox_field name="announcement[notify_via_email]" checked=$announcement_data.notify_via_email label="Notify via Email Now" id=notify_via_email}
{/wrap}

<script type="text/javascript">
  var radio_buttons = $('#select_icon .radio_group_option input[type=radio]');

  radio_buttons.addClass('input_hidden');

  App.each(radio_buttons, function() {
    var radio_button = $(this);

    if(radio_button.is(':checked')) {
      radio_button.next().addClass('selected');
    } // if
  });

  var labels = $('#select_icon .radio_group_option label');

  App.each(labels, function() {
    var label = $(this);
    var icon_name = label.prev().val();
    var announcement_type = label.html();

    label.html('<img src="' + App.Wireframe.Utils.imageUrl('icons/16x16/' + icon_name + '.png', '{$smarty.const.ANNOUNCEMENTS_FRAMEWORK}') + '" alt="" title="' + announcement_type + '" />');
  });

  labels.click(function() {
    $(this).addClass('selected').parent().siblings('.radio_group_option').find('label').removeClass('selected');
  });
</script>