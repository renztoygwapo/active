<?php
  /**
   * Convert environment module rawtext to richtext
   *
   * @package angie.framework.environment
   * @subpackage handlers
   */

  /**
   * do rawtext to richtext conversion
   * 
   * @param simple_html_dom $parser
   * @param string $for
   */
  function environment_handle_on_rawtext_to_richtext($parser, $for = null) {
  	
    // <a object-id="*" object-class="*">User: Goran Radulović</a>
    $object_links = $parser->find('a[object-id][object-class]');
    if (is_foreachable($object_links)) {
      foreach ($object_links as $object_link) {
      	$object_id = array_var($object_link->attr, 'object-id', null);
      	$object_class = Inflector::camelize(array_var($object_link->attr, 'object-class', null));
      	
      	if ($object_id && $object_class) {
      		try {
            $object = DataObjectPool::get($object_class, $object_id);

						if (!($object instanceof ApplicationObject)) {
							throw new InvalidInstanceError('object', $object, 'ApplicationObject');
						} // if

            if ($object_link->innertext) {
              $inner_text = $object_link->innertext;
            } else if ($object_link->plaintext) {
              $inner_text = $object_link->plaintext;
            } else {
              $inner_text = $object->getVerboseType() . ': ' . clean($object->getName());
            } // if

            // check if we need to open link in new window
            $in_new_window = strtolower(array_var($object_link->attr, 'target', '')) == '_blank';
						$object_link->outertext = '<a href="' . $object->getViewUrl() . '" ' . ($in_new_window ? 'target="_blank"' : '') . '>' . $inner_text . '</a>';
      		} catch (Exception $e) {
						$object_link->outertext = ''; 
      		} // if
      	} else {
      		$object_link->outertext = '';
      	} // if      	
      } // foreach
    } // if

  } // environment_handle_on_rawtext_to_richtext