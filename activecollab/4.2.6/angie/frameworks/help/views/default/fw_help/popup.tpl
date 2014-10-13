<div id="help_popup">
  <div id="help_popup_sections">
    <ul>
      <li data-section-url="{assemble route=help_whats_new}"><img src="{image_url name="shortcuts/whats-new.png" module=$smarty.const.HELP_FRAMEWORK}"><span> {lang}What's New{/lang}</span></li>
      <li data-section-url="{assemble route=help_books}"><img src="{image_url name="shortcuts/books.png" module=$smarty.const.HELP_FRAMEWORK}"><span> {lang}Manuals and Guides{/lang}</span></li>
      <li data-section-url="{assemble route=help_videos}"><img src="{image_url name="shortcuts/videos.png" module=$smarty.const.HELP_FRAMEWORK}"><span> {lang}Instructional Videos{/lang}</span></li>
    </ul>
  </div>

{if is_foreachable($common_questions)}
  <div id="help_popup_common_questions">
    <h3>{lang}Frequently Asked Questions{/lang}</h3>
    <ul>
      {foreach $common_questions as $common_question}
        <li><a href="{$common_question.page_url}">{$common_question.question}</a></li>
      {/foreach}
    </ul>
    {if $total_common_questions > count($common_question)}
      {assign var=more_questions value=$total_common_questions-$showing_common_questions}
      <p><a href="{assemble route=help}" class="more_questions">{lang}More{/lang} {if $more_questions > 0}({$more_questions}){/if} &raquo;</a></p>
    {/if}
  </div>
{/if}

{if $contact_options && is_foreachable($contact_options)}
  <div id="help_popup_contact">
    <h3>{lang}Contact Customer Service{/lang}</h3>
    <ul>
    {foreach $contact_options as $contact_option_name => $contact_option}
      <li id="help_contact_{$contact_option_name}" class="{if empty($contact_option.description)}without_description{/if}">
        <span class="contact_method">
        {if $contact_option.onclick instanceof JavaScriptCallback}
          <a href="{$contact_option.url}" id="help_contact_{$contact_option_name}_link">{$contact_option.text}</a>

          <script type="text/javascript">
            $('#help_contact_{$contact_option_name}_link').each(function() {
              var onclick;
              eval('onclick = ' + {$contact_option.onclick->render()|var_export:true nofilter});

              if(typeof(onclick) == 'function') {
                onclick.apply(this);
              } // if
            });
          </script>
        {else}
          {$contact_option.text}
        {/if}
        </span>
      {if $contact_option.description}
        <span class="contact_method_description">{$contact_option.description}</span>
      {/if}
      </li>
    {/foreach}
    </ul>
{/if}
</div>

<script type="text/javascript">
  $('#help_popup').each(function() {
    var wrapper = $(this);

    wrapper.on('click', '#help_popup_sections ul li', function(e) {
      $('#global_help').contextPopup('close');
      App.Wireframe.Content.setFromUrl($(this).data('sectionUrl'));

      e.stopPropagation();
      return false;
    });

    wrapper.on('click', '#help_popup_common_questions a', function(e) {
      $('#global_help').contextPopup('close');
      App.Wireframe.Content.setFromUrl($(this).attr('href'));

      e.preventDefault();
      e.stopPropagation();

      return true;
    })
  });
</script>