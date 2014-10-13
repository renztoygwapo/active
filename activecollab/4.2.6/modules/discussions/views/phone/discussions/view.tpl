{title lang=false}{$active_discussion->getName()}{/title}
{add_bread_crumb}Details{/add_bread_crumb}

{object object=$active_discussion user=$logged_user}
	<div class="wireframe_content_wrapper">
		<div class="object_content_wrapper"><div class="object_body_content">
			<p class="comment_details ui-li-desc">{lang}By{/lang} {user_link user=$active_discussion->getCreatedBy()} {lang}on{/lang} {$active_discussion->getCreatedOn()|ago nofilter}</p>
			<p>{$active_discussion->getBody()|rich_text nofilter}</p>
		</div></div>
	</div>

  {object_comments object=$active_discussion user=$logged_user interface=AngieApplication::INTERFACE_PHONE id=discussion_comments}
  {render_comment_form object=$active_discussion id=discussion_comments}
{/object}