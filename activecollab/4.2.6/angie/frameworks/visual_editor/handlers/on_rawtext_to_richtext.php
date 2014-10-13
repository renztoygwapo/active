<?php

  /**
   * visual editor module rawtext to richtext
   *
   * @package angie.framework.visual_editor
   * @subpackage handlers
   */

  /**
   * do rawtext to richtext conversion
   *
   * @param simple_html_dom $parser
   * @param string $for
   */
  function visual_editor_handle_on_rawtext_to_richtext($parser, $for = null) {

    // <div placeholder-type="code">
    $code_snippet_placeholders = $parser->find('div[placeholder-type=code]');
    if (is_foreachable($code_snippet_placeholders)) {
    	foreach ($code_snippet_placeholders as $code_snippet_placeholder) {
    		$code_snippet_id = array_var($code_snippet_placeholder->attr, 'placeholder-object-id', null);
				if ($code_snippet_id) {
					try {
						$code_snippet = CodeSnippets::findById($code_snippet_id);

            if($code_snippet instanceof CodeSnippet) {
              $code_snippet_placeholder->outertext = $for === 'notification' || $for == 'frontend' || $for === 'printer' ? $code_snippet->renderPlain() : $code_snippet->render();
            } else {
              throw new InvalidInstanceError('code_snippet', $code_snippet, 'CodeSnippet');
            } // if
					} catch (Exception $e) {
						$code_snippet_placeholder->outertext = '';
					} // try
				} else {
					$code_snippet_placeholder->outertext = '';
				} // if
    	} // foreach
    } // if

    // <div placeholder-type="video">
    $video_placeholders = $parser->find('div[placeholder-type=video]');
    if ($video_placeholders && is_foreachable($video_placeholders)) {
      foreach ($video_placeholders as $video_placeholder) {
        $video_service = array_var($video_placeholder->attr, 'placeholder-extra', 'youtube');
        $video_id = array_var($video_placeholder->attr, 'placeholder-object-id', null);

        if($for === 'notification' || $for === 'printer') {
          if($video_service == 'youtube') {
            $video_placeholder->outertext = '<p>' . lang('Video') . ': <a href="http://www.youtube.com/watch?v=' . clean($video_id) . '" target="_blank">http://www.youtube.com/watch?v=' . clean($video_url) . '</p>';
          } else {
            $video_placeholder->outertext = '<p>' . lang('Unknown video service') . '</p>';
          } // if
        } else {
          if($video_service == 'youtube') {
            $video_placeholder->outertext = '<div class="youtube_video_wrapper" style="text-align: center;"><div class="youtube_video_wrapper_innner" style="margin: 8px auto"><iframe width="550" height="335" src="//www.youtube.com/embed/' . clean($video_id) . '?theme=light&wmode=opaque" frameborder="0" allowfullscreen></iframe></div></div>';
          } else {
            $video_placeholder->outertext = '<p>' . lang('Unknown video service') . '</p>';
          } // if
        } // if
      } // foreach
    } // if

    // <img object-id="*" image-type="attachment">
    $inline_image_placeholder_placeholders = $parser->find('img[image-type=attachment]');
    if (is_foreachable($inline_image_placeholder_placeholders)) {
      if($for === 'notification') {
        $max_inline_object_width = 500;
        $max_inline_object_height = 500;
      } else {
        $max_inline_object_width = 800;
        $max_inline_object_height = 800;
      } // if

    	foreach ($inline_image_placeholder_placeholders as $inline_image_placeholder) {
    		$image_id = array_var($inline_image_placeholder->attr, 'object-id', null);
    		if ($image_id) {
    			try {
    				$inline_image = Attachments::findById($image_id);

            if($inline_image instanceof Attachment && $inline_image->getState() > STATE_DELETED) {
              if ($inline_image_placeholder->parent && $inline_image_placeholder->parent->tag && $inline_image_placeholder->parent->tag == 'a') {
                $inline_image_placeholder->outertext = '<div style="text-align: center"><img src="' . clean(Thumbnails::getUrl($inline_image->getFilePath(), $max_inline_object_width, $max_inline_object_height)) . '" alt="' . clean($inline_image->getName()) . '" /></div>';
              } else {
                $inline_image_placeholder->outertext = '<div style="text-align: center"><a href="' . clean($inline_image->getPublicViewUrl()) . '" target="_blank"><img src="' . clean(Thumbnails::getUrl($inline_image->getFilePath(), $max_inline_object_width, $max_inline_object_height)) . '" alt="' . clean($inline_image->getName()) . '" /></a></div>';
              } // if
            } else {
              throw new InvalidInstanceError('inline_image', $inline_image, 'Attachment');
            } // if
    			} catch (Exception $e) {
    				$inline_image_placeholder->outertext = '';
    			} // try
    		} else {
    			$inline_image_placeholder->outertext = '';
    		} // if
    	} // if
    } // if
  } // visual_editor_handle_on_rawtext_to_richtext