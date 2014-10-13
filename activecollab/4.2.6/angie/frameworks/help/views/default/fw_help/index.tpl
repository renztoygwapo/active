{title}Welcome to activeCollab Help Center{/title}
{add_bread_crumb}Welcome{/add_bread_crumb}

<div id="help_welcome"  class="wireframe_content_wrapper">
  {search_help}

  <ul id="help_shortcuts">
    <li>
      <a href="{assemble route="help_whats_new"}">
        <span class="help_shortcut_title">{lang}What's New{/lang}</span>
        <span class="help_shortcut_image">{image name="shortcuts/whats-new.png" module="help"}</span>
        <span class="help_shortcut_details">{lang}Stay up to date on the latest features and enhancements{/lang}</span>
      </a>
    </li>
    <li>
      <a href="{assemble route="help_books"}">
        <span class="help_shortcut_title">{lang}User Manuals and Guides{/lang}</span>
        <span class="help_shortcut_image">{image name="shortcuts/books.png" module="help"}</span>
        <span class="help_shortcut_details">{lang}Everything you ever wanted to know about activeCollab{/lang}</span>
      </a>
    </li>
    <li>
      <a href="{assemble route="help_videos"}">
        <span class="help_shortcut_title">{lang}Instructional Videos{/lang}</span>
        <span class="help_shortcut_image">{image name="shortcuts/videos.png" module="help"}</span>
        <span class="help_shortcut_details">{lang}Have a look at our tutorial videos and become an activeCollab expert in no time{/lang}</span>
      </a>
    </li>
  </ul>

  {if is_foreachable($common_questions)}
  <div id="help_common_questions">
    <h3>{lang}Frequently Asked Questions{/lang}</h3>
    <ul>
    {foreach $common_questions as $common_question}
      <li><a href="{$common_question.page_url}">{$common_question.question}</a></li>
    {/foreach}
    </ul>
  </div>
  {/if}
</div>