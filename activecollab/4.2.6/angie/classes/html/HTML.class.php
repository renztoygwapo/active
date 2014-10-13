<?php

  /**
   * HTML tag generator class
   * 
   * @package angie.library
   */
  final class HTML {
    
    /**
     * Render field label
     *
     * @param string $text
     * @param string $for
     * @param boolean $required
     * @param array $attributes
     * @param string $after_label
     * @return string
     */
    static function label($text, $for = null, $required = false, $attributes = null, $after_label = ':') {
      if($for) {
        if($attributes) {
          $attributes['for'] = $for;
        } else {
          $attributes = array('for' => $for);
        } // if
      } // if

      // do the lang thingy
      $text = lang($text);
      
      $render_text = clean($text) . $after_label;
      
      if($required) {
        $render_text .= ' <span class="label_required">*</span>';
      } // if
      
      return self::openTag('label', $attributes) . $render_text . '</label>';
    } // label
    
    // ---------------------------------------------------
    //  Inputs
    // ---------------------------------------------------
    
    /**
     * Render input element
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string
     */
    static function input($name, $value, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['name'] = $name;
      $attributes['value'] = $value;

      if(!isset($attributes['type']) || empty($attributes['type'])) {
        $attributes['type'] = 'text';
      } // if
      
      $label = isset($attributes['label']) && $attributes['label'] ? $attributes['label'] : null;
      
      if($label) {
        unset($attributes['label']);
        
        if(empty($attributes['id'])) {
          $attributes['id'] = self::uniqueId('input');
        } // if
        
        return self::label($label, $attributes['id'], (isset($attributes['required']) && $attributes['required']), array('class' => 'main_label')) . self::openTag('input', $attributes);
      } else {
        return self::openTag('input', $attributes);
      } // if
    } // input
    
    /**
     * Render and return radio input
     * 
     * @param string $name
     * @param boolean $checked
     * @param array $attributes
     * @return string
     */
    static function radio($name, $checked = false, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'radio';
      $attributes['name'] = $name;
      
      if(!isset($attributes['value'])) {
        $attributes['value'] = 'checked';
      } // if

      if ((boolean) $checked) {
        $attributes['checked'] = 'checked';
      } // if
      
      $label = isset($attributes['label']) ? $attributes['label'] : null;
      if($label) {
        unset($attributes['label']);
        
        if(empty($attributes['id'])) {
          $attributes['id'] = self::uniqueId('radio');
        } // if
        
        return self::openTag('label', array('for' => $attributes['id'])) . self::openTag('input', $attributes) . ' ' . clean($label) . '</label>';
      } else {
        return self::openTag('input', $attributes);
      } // if
    } // radio
    
    /**
     * Render and return checkbox
     * 
     * @param string $name
     * @param boolean $checked
     * @param array $attributes
     * @return string
     */
    static function checkbox($name, $checked = false, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'checkbox';
      $attributes['checked'] = (boolean) $checked;
      $attributes['name'] = $name;
      
      if(!isset($attributes['value'])) {
        $attributes['value'] = 'checked';
      } // if
      
      $label = isset($attributes['label']) ? $attributes['label'] : null;
      if($label) {
        unset($attributes['label']);
        
        if(empty($attributes['id'])) {
          $attributes['id'] = self::uniqueId('checkbox');
        } // if
        
        return self::openTag('label', array('for' => $attributes['id'])) . self::openTag('input', $attributes) . ' ' . clean($label) . '</label>';
      } else {
        return self::openTag('input', $attributes);
      } // if
    } // checkbox
    
    /**
     * Upload file input
     * 
     * @param string $name
     * @param array $attributes
     * @return string
     */
    static function file($name, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'file';
      $attributes['name'] = $name;
      
      $label = isset($attributes['label']) ? $attributes['label'] : null;
      if($label) {
        unset($attributes['label']);
        
        if(empty($attributes['id'])) {
          $attributes['id'] = self::uniqueId('file');
        } // if
        
        return self::label($label, $attributes['id'], (isset($attributes['required']) && $attributes['required']), array('class' => 'main_label')) . self::openTag('input', $attributes);
      } else {
        return self::openTag('input', $attributes);
      } // if
    } // file
    
    /**
     * Return number input
     * 
     * @param string $name
     * @param mixed $value
     * @param array $attributes
     * @return string
     */
    function number($name, $value = '', $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'number';
      
      return self::input($name, $value, $attributes);
    } // number
    
    /**
     * Render email tag
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string
     */
    static function email($name, $value = '', $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'email';
      
      return self::input($name, $value, $attributes);
    } // email
    
    /**
     * Render URL tag
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string
     */
    static function url($name, $value = '', $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['type'] = 'url';
      $attributes['placeholder'] = 'http://';
      
      return self::input($name, $value, $attributes);
    } // url
    
    // ---------------------------------------------------
    //  Text
    // ---------------------------------------------------
    
    /**
     * Render textarea control
     * 
     * @param string $name
     * @param string $value
     * @param array $attributes
     * @return string
     */
    static function textarea($name, $value = '', $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['name'] = $name;
      
      $label = isset($attributes['label']) && $attributes['label'] ? $attributes['label'] : null;
      
      if (empty($attributes['id'])) {
      	$attributes['id'] = self::uniqueId('textarea');
      } // if
      
      $max_length = array_var($attributes, 'maxlength', 0);
      $return = '';
      
      if($label) {
        unset($attributes['label']);
        $return = self::label($label, $attributes['id'], (isset($attributes['required']) && $attributes['required']), array('class' => 'main_label')) . self::openTag('textarea', $attributes) . ($value ? clean($value) : $value) . '</textarea>';
      } else {
        $return = self::openTag('textarea', $attributes) . ($value ? clean($value) : $value) . '</textarea>';
      } // if
      
      if ($max_length) {
      	$return.= '<script type="text/javascript">$("#' . $attributes['id'] . '").maxlength();</script>';
      } // if
      
      return $return;
    } // textarea
    
    // ---------------------------------------------------
    //  Select and groups
    // ---------------------------------------------------
    
    /**
     * Render select box based on options list and provided settings
     * 
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @return string
     */
    static function select($name, $options = null, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      if(empty($attributes['id'])) {
        $attributes['id'] = self::uniqueId('select_box');
      } // if

      // IE10 fix for https://connect.microsoft.com/IE/feedback/details/787135/select-boxes-using-the-html5-required-attribute-and-using-optgroups-are-not-accepted-as-valid-even-if-a-value-was-selected
      if(isset($attributes['required']) && $attributes['required'] && isset($_SERVER) && preg_match('/(?i)msie [10]/', $_SERVER['HTTP_USER_AGENT'])) {
        unset($attributes['required']);
      } // if

      if(isset($attributes['label']) && $attributes['label']) {
        $required = isset($attributes['required']) && $attributes['required'];

        $prefix = self::label($attributes['label'], $attributes['id'], $required, array('class' => 'main_label'));
        unset($attributes['label']);
      } else {
        $prefix = '';
      } // if
      
      $attributes['name'] = $name;

			if(AngieApplication::getPreferedInterface() == AngieApplication::INTERFACE_PHONE) {
        $attributes['data-native-menu'] = 'false';
     	} // if
        
			$return = $prefix . self::openTag('select', $attributes);
			
			if($options) {
     	  $return .= is_array($options) ? implode("\n", $options) : (string) $options;
     	} // if
     	
     	return "$return</select>";
    } // select
    
    /**
     * Render option group
     * 
     * @param string $text
     * @param array $options
     * @param array $attributes
     * @return string
     */
    static function optionGroup($text, $options = null, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['label'] = $text;
      
      return self::openTag('optgroup', $attributes) . ($options ? implode("\n", $options) : '') . '</optgroup>';
    } // optionGroup
    
    /**
     * Render optional select box based on list of options and provded settings
     * 
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @param string $optional_text
     * @param int $optional_value
     * @return string
     */
    static function optionalSelect($name, $options = null, $attributes = null, $optional_text = null, $optional_value = 0) {
      if(empty($optional_text)) {
        $optional_text = lang('None');
      } // if
      
      $interface = array_var($attributes, 'interface', AngieApplication::getPreferedInterface());
      
      // Default interface
      if($interface == AngieApplication::INTERFACE_DEFAULT) {
        $optional_options = array(self::optionForSelect($optional_text, $optional_value));
        if($options) {
          $optional_options[] = self::optionForSelect('', '');
        } // if
      } else {
        $optional_options = array(self::optionForSelect(lang('Please Select'), ''));
        
        if(!array_var($attributes, 'multiple')) {
          $optional_options[] = self::optionForSelect($optional_text, 0);
        } // if
      } // if

      if (is_string($options)) {
      	$merged_options = implode('', $optional_options) . $options;
      } else if (is_array($options)) {
      	$merged_options = array_merge($optional_options, $options);
      } else {
      	$merged_options = $options;
      } // if
      
      return self::select($name, $merged_options, $attributes);
    } // optionalSelect
    
    /**
     * Render radio group based on list of options that's provided
     * 
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function radioGroup($name, $options, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      if(isset($attributes['class']) && $attributes['class']) {
        $attributes['class'] .= ' radio_group';
      } else {
        $attributes['class'] = 'radio_group';
      } // if
      
      return self::optionsGroup($name, $options, $attributes, $interface);
    } // radioGroup
    
    /**
     * Render checkbox group based on list of options that are provided
     * 
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function checkboxGroup($name, $options, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      if(isset($attributes['class']) && $attributes['class']) {
        $attributes['class'] .= ' checkbox_group';
      } else {
        $attributes['class'] = 'checkbox_group';
      } // if
      
      return self::optionsGroup($name, $options, $attributes, $interface);
    } // checkboxGroup
    
    /**
     * Render option group
     * 
     * @param string $name
     * @param array $options
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static private function optionsGroup($name, $options, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      if($interface == AngieApplication::INTERFACE_PHONE || $interface == AngieApplication::INTERFACE_TABLET) {
        if(isset($attributes['class'])) {
          $attributes['class'] .= ' radio_group';
        } else {
          $attributes['class'] = 'radio_group';
        } // if
        
        $attributes['data-role'] = 'controlgroup';
        if(array_var($attributes, 'orientation', null, true) == 'horizontal') {
          $attributes['data-type'] = 'horizontal';
        } else {
          $attributes['data-type'] = 'vertical';
        } // if
         
        //$result = '<fieldset data-role="controlgroup" data-type="horizontal" data-role="fieldcontain"><legend>' . lang('Interface') . '</legend>';
        
        $result = self::openTag('fieldset', $attributes);
        
        if(isset($attributes['label'])) {
          $result .= '<legend>' . clean($attributes['label']) . '</legend>';
          unset($attributes['label']);
        } // if
        
        if($options) {
          $result .= implode("\n", $options);
        } // if
        
        return $result . '</fieldset>';
      } else {
        if(isset($attributes['label']) && $attributes['label']) {
          $prefix = self::label($attributes['label'], null, (boolean) $attributes['required'], array('class' => 'main_label'));
          unset($attributes['label']);
        } else {
          $prefix = '';
        } // if
        
        return self::openTag('div', $attributes) . $prefix . ($options ? implode("\n", $options) : '') . '</div>';
      } // if
    } // optionsGroup
    
    // ---------------------------------------------------
    //  Controls from possibilities
    // ---------------------------------------------------
    
    /**
     * Render select box based on list of possibilities
     * 
     * @param string $name
     * @param array $possibilities
     * @param mixed $selected
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function selectFromPossibilities($name, $possibilities, $selected, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $options = array();

      foreach($possibilities as $k => $v) {
        $options[] = self::optionForSelect($v, $k, $k == $selected, null, $interface);
      } // foreach
      
      return self::select($name, $options, $attributes, $interface);
    } // selectFromPossibilities

    /**
     * Render optional select from the list of possibilities
     *
     * @param string $name
     * @param array $possibilities
     * @param mixed $selected
     * @param array $attributes
     * @param string $optional_text
     * @param int $optional_value
     * @param string $interface
     * @return string
     */
    static function optionalSelectFromPossibilities($name, $possibilities, $selected, $attributes = null, $optional_text = null, $optional_value = 0, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $options = array();

      foreach($possibilities as $k => $v) {
        $options[] = self::optionForSelect($v, $k, (string) $k == (string) $selected, null, $interface);
      } // foreach
      
      return self::optionalSelect($name, $options, $attributes, $optional_text, $optional_value, $interface);
    } // optionalSelectFromPossibilities
    
    /**
     * Render radio group based on list of possibilities
     * 
     * @param string $name
     * @param array $possibilities
     * @param mixed $selected
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function radioGroupFromPossibilities($name, $possibilities, $selected = null, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if($selected === null || !in_array($selected, array_keys($possibilities))) {
        $selected = first($possibilities, true); // Make sure that we have one item selected
      } // if
      
      $inline = array_var($attributes, 'inline', false, true);
      
      $options = array();

      foreach($possibilities as $k => $v) {
        if($inline) {
          $options[] = '<span class="radio_group_option">' . self::optionForRadioGroup($name, $k, $v, $k == $selected, null, $interface) . '</span>';
        } else {
          $options[] = '<div class="radio_group_option">' . self::optionForRadioGroup($name, $k, $v, $k == $selected, null, $interface) . '</div>';
        } // if
      } // foreach
      
      return self::radioGroup($name, $options, $attributes, $interface);
    } // radioGroupFromPossibilities
    
    /**
     * Render checkbox group based on list of possibilities
     * 
     * @param string $name
     * @param array $possibilities
     * @param mixed $selected
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function checkboxGroupFromPossibilities($name, $possibilities, $selected, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      $options = array();

      foreach($possibilities as $k => $v) {
        $options[] = '<div class="checkbox_group_option">' . self::optionForCheckboxGroup($name . '[]', $k, $v, (is_array($selected) ? in_array($k, $selected) : $k == $selected), null, $interface) . '</div>';
      } // foreach
      
      return self::checkboxGroup($name, $options, $attributes, $interface);
    } // checkboxGroupFromPossibilities
    
    // ---------------------------------------------------
    //  Options
    // ---------------------------------------------------
    
    /**
     * Render option for select box
     * 
     * @param string $text
     * @param mixed $value
     * @param boolean $selected
     * @param array $attributes
     * @return string
     */
    static function optionForSelect($text, $value = null, $selected = false, $attributes = null) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['value'] = $value;
      $attributes['selected'] = (boolean) $selected;
      
      return self::openTag('option', $attributes) . ($text ? clean($text) : $text) . '</option>';
    } // optionForSelect
    
    /**
     * Render option for use in radio group
     *
     * @param string $name
     * @param mixed $value
     * @param string $text
     * @param boolean $selected
     * @param array $attributes
     * @param string $interface
     * @return string 
     */
    static function optionForRadioGroup($name, $value, $text, $selected = false, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['value'] = $value;
      
      if(!isset($attributes['id'])) {
        $attributes['id'] = self::uniqueId();
      } // if
      
      return self::radio($name, $selected, $attributes) . ' ' . self::label($text, $attributes['id'], false, null, '');
    } // optionForRadioGroup
    
    /**
     * Render option for use in checkbox group
     * 
     * @param string $name
     * @param mixed $value
     * @param string $text
     * @param boolean $selected
     * @param array $attributes
     * @param string $interface
     * @return string
     */
    static function optionForCheckboxGroup($name, $value, $text, $selected = false, $attributes = null, $interface = AngieApplication::INTERFACE_DEFAULT) {
      if(empty($attributes)) {
        $attributes = array();
      } // if
      
      $attributes['value'] = $value;
      
      if(!isset($attributes['id'])) {
        $attributes['id'] = self::uniqueId();
      } // if
      
      return self::checkbox($name, $selected, $attributes) . ' ' . self::label($text, $attributes['id'], false, null, '');
    } // optionForCheckboxGroup
    
    // ---------------------------------------------------
    //  Utilities
    // ---------------------------------------------------
    
    /**
     * Open HTML tag
     * 
     * @param string $name
     * @param array $attributes
     * @param Closure|string|null $content
     * @return string
     */
    static function openTag($name, $attributes = null, $content = null) {
      if($attributes) {
        $result = "<$name";
        
        foreach($attributes as $k => $v) {
          if($k) {
            if(is_bool($v)) {
              if($v) {
                $result .= " $k";
              } // if
            } else {
              $result .= ' ' . $k . '="' . ($v ? clean($v) : $v) . '"';
            } // if
          } // if
        } // foreach

        $result .= '>';
      } else {
        $result = "<$name>";
      } // if

      if($content) {
        if($content instanceof Closure) {
          $result .= $content();
        } else {
          $result .= $content;
        } // if

        $result .= "</$name>";
      } // if

      return $result;
    } // openTag
    
    /**
     * Used ID-s in this request
     *
     * @var array
     */
    static private $used_ids = array();
    
    /**
     * Prefix used for all elements that don't have $precix defined
     *
     * @var string
     */
    static private $random_for_id = false;
    
    /**
     * Return session wide unique ID for given prefix
     * 
     * @param string $prefix
     * @return string
     */
    static function uniqueId($prefix = '') {
      if(empty(self::$random_for_id)) {
        self::$random_for_id = 'element_' . time() . '_' . mt_rand();
      } // if
      
      if(empty($prefix)) {
        $prefix = APPLICATION_NAME . '_' . self::$random_for_id . '_element';
      } else {
        $prefix = APPLICATION_NAME . '_' . self::$random_for_id . '_' . $prefix;
      } // if
      
      do {
        $id = $prefix . '_' . mt_rand();
      } while(in_array($id, self::$used_ids));
      
      self::$used_ids[] = $id;
      
      return $id;
    } // uniqueId
    
    // ---------------------------------------------------
    //  Converters
    // ---------------------------------------------------
    
    /**
     * Convert HTML to plain text (email style)
     * 
     * @param string $html
     * @param boolean $clean
     * @return string
     */
    static function toPlainText($html, $clean = false) {
      // store it in local variable
      if($clean) {
        $plain = (string) HTML::cleanUpHtml($html);
      } else {
        $plain = (string) $html;
      } // if

      // strip slashes
      $plain = (string) trim(stripslashes($plain));

      // strip unnecessary characters
      $plain = (string) preg_replace(array(
        "/\r/", // strip carriage returns
        "/<script[^>]*>.*?<\/script>/si", // strip immediately, because we don't need any data from it
        "/<style[^>]*>.*?<\/style>/is", // strip immediately, because we don't need any data from it
        "/style=\".*?\"/"   //was: '/style=\"[^\"]*/'
      ), "", $plain);

      // entities to convert (this is not a definite list)
      $entities = array(
        ' '     => array('&nbsp;', '&#160;'),
        '"'     => array('&quot;', '&rdquo;', '&ldquo;', '&#8220;', '&#8221;', '&#147;', '&#148;'),
        '\''    => array('&apos;', '&rsquo;', '&lsquo;', '&#8216;', '&#8217;'),
        '>'     => array('&gt;'),
        '<'     => array('&lt;'),
        '&'     => array('&amp;', '&#38;'),
        '(c)'   => array('&copy;', '&#169;'),
        '(R)'   => array('&reg;', '&#174;'),
        '(tm)'  => array('&trade;', '&#8482;', '&#153;'),
        '--'    => array('&mdash;', '&#151;', '&#8212;'),
        '-'     => array('&ndash;', '&minus;', '&#8211;', '&#8722;'),
        '*'     => array('&bull;', '&#149;', '&#8226;'),
        '�'     => array('&pound;', '&#163;'),
        'EUR'   => array('&euro;', '&#8364;')
      );

      // convert specified entities
      foreach ($entities as $character => $entity) {
        $plain = (string) str_replace_utf8($entity, $character, $plain);
      } // foreach

      // strip other not previously converted entities
      $plain = (string) preg_replace(array(
        '/&[^&;]+;/si',
      ), "", $plain);

      // <p> converts to 2 newlines
      $plain = (string) preg_replace('/<p[^>]*>/i', "\n\n", $plain); // <p>

      // uppercase html elements
      $plain = (string) preg_replace_callback('/<h[123456][^>]*>(.*?)<\/h[123456]>/i', function($matches) {
        return "\n\n" . strtoupper_utf($matches[1]) . "\n\n";
      }, $plain); // <h1-h6>

      $plain = (string) preg_replace_callback(array('/<b[^>]*>(.*?)<\/b>/i', '/<strong[^>]*>(.*?)<\/strong>/i'), function($matches) {
        return strtoupper_utf($matches[1]);
      }, $plain); // <b> <strong>
      
      // deal with italic elements
      $plain = (string) preg_replace(array('/<i[^>]*>(.*?)<\/i>/i', '/<em[^>]*>(.*?)<\/em>/i'), '_\\1_', $plain); // <i> <em>

      // elements that convert to 2 newlines
      $plain = (string) preg_replace(array('/(<ul[^>]*>|<\/ul>)/i', '/(<ol[^>]*>|<\/ol>)/i', '/(<table[^>]*>|<\/table>)/i'), "\n\n", $plain); // <ul> <ol> <table>

      // elements that convert to single newline
      $plain = (string) preg_replace(array('/<br[^>]*>/i', '/(<tr[^>]*>|<\/tr>)/i'), "\n", $plain); // <br> <tr>

      // <hr> converts to -----------------------
      $plain = (string) preg_replace('/<hr[^>]*>/i', "\n-------------------------\n", $plain); // <hr>

      // other table tags
      $plain = (string) preg_replace('/<td[^>]*>(.*?)<\/td>/i', "\t\\1\n", $plain); // <td>
      $plain = (string) preg_replace_callback('/<th[^>]*>(.*?)<\/th>/i', function($matches) {
        return "\t\t" . strtoupper_utf($matches) . "\n";
      }, $plain); // <th>
      
      // list elements
      $plain = (string) preg_replace('/<li[^>]*>(.*?)<\/li>/i', "* \\1\n", $plain); // <li>with content</li>
      $plain = (string) preg_replace('/<li[^>]*>/i', "\n* ", $plain); // <li />

      // handle anchors
      $plain = (string) preg_replace_callback('/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/i', function($matches) {
        return HTML::toPlainTextProcessUrl($matches[1], $matches[2]);
      }, $plain); // <li />

      // handle blockquotes
      $plain = (string) preg_replace_callback('/<blockquote[^>]*>(.*?)<\/blockquote>/is', function ($blockquote_content) {
        $blockquote_content = isset($blockquote_content[1]) ? $blockquote_content[1] : '';

        $lines = (array) explode("\n", $blockquote_content);
        $return = array();
        if (is_foreachable($lines)) {
          foreach ($lines as $line) {
            $return[] = '> ' . $line;
          } // if
        } // if
        return "\n\n" . implode("\n", $return) . "\n\n";
      }, $plain);

      // strip other tags
      $plain = (string) strip_tags($plain);

      // clean up unneccessary newlines
      $plain = (string) preg_replace("/\n\s+\n/", "\n\n", $plain);
      $plain = (string) preg_replace("/[\n]{3,}/", "\n\n", $plain);

      return trim($plain);
    } // toPlainText


    /**
     * Validate that HTML data exists in provided HTML
     *
     * @param String $html
     * @param bool $min_length
     * @return bool
     */
    static function validateHTML($html, $min_length = false) {
      if ($html) {
        // strip (excluding specified tags)
        $html = (string) strip_tags($html, '<div><img><a>');

        // remove non ascii characters
        //$html = preg_replace('/[^(\x20-\x7F)]*/', '', $html);

        // trim the html
        $html = (string) trim($html);

        if ($min_length) {
          return strlen_utf($html) >= $min_length;
        } else {
          return (boolean) $html;
        } // if
      } else {
        return false;
      } // if
    } // validateHTML
    
    /**
     * This function is used as a callback in html_to_text function to process 
     * links found in the text
     *
     * @param string $url
     * @param string $text
     * @return string
     */
    static function toPlainTextProcessUrl($url, $text) {
      if(str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
        return "$text [$url]";
      } elseif(str_starts_with($url, 'mailto:')) {
        return $text . ' [' . substr($url, 7) . ']';
      } else {
        return $text;
      } // if
    } // toPlainTextProcessUrl
    
    /**
     * Cached whitelisted tags
     * 
     * @var array
     */
    static $whitelisted_tags = false;
    
    /**
     * load whitelisted tags from config option
     */
    static function loadWhitelistedTags() {
    	if (self::$whitelisted_tags === false) {
    		self::$whitelisted_tags = array();

    		$config_whitelisted_tags = array(
          'environment' => array(
            'br' => null,
            'div' => array('class', 'placeholder-type', 'placeholder-object-id', 'placeholder-extra', 'align'),
            'span' => array('style', 'class', 'align', 'object-id', 'data-user-id'),
            'a' => array('href', 'title', 'class', 'object-id', 'object-class', 'align', 'target'),
            'img' => array('src', 'alt', 'title', 'class', 'align'),
            'p' => array('class', 'align'),
            'blockquote' => array('class'),
            'ul' => array('class', 'align'),
            'ol' => array('class', 'align'),
            'li' => array('class', 'align'),
            'b' => null, 'strong' => null,
            'i' => null, 'em' => null,
            'u' => null,
            'del' => null,
            'table' => null,
            'thead' => null,
            'tbody' => null,
            'tfoot' => null,
            'tr' => null,
            'td' => array('align', 'class', 'colspan', 'rowspan'),
            'th' => array('align'),
            'h1' => array('align'),
            'h2' => array('align'),
            'h3' => array('align')
          ),
          'visual_editor' => array(
	          'p' => array('class', 'style'),
	          'img' => array('image-type', 'object-id', 'class'),
	          'strike' => array('class', 'style'),
	          'span' => array('class', 'data-redactor-inlinemethods', 'data-redactor'),
	          'a' => array('class', 'href'),
	          'blockquote' => null,
	          'br' => null,
	          'b' => null, 'strong' => null,
	          'i' => null, 'em' => null,
	          'u' => null
          ),
        );

	    	if (is_foreachable($config_whitelisted_tags)) {
	    		foreach ($config_whitelisted_tags as $module) {
	    			if (is_foreachable($module)) {
	    				foreach ($module as $whitelisted_tag => $whitelisted_tag_attributes) {
	    					self::$whitelisted_tags[$whitelisted_tag] = array_merge(isset(self::$whitelisted_tags[$whitelisted_tag]) ? self::$whitelisted_tags[$whitelisted_tag] : array(), (array) $whitelisted_tag_attributes);
	    				} // foreach
	    			} // if
	    		} // foreach
	    	} // if
    	} // if
    } // loadWhitelistedTags
    
    /**
     * Return whitelisted tags specially for purifier
     * 
     * @return array
     */
    static function getWhitelistedTagsForPurifier() {
    	if (self::$whitelisted_tags === false) {
    		self::loadWhitelistedTags();
    	} // if
    	
    	return self::$whitelisted_tags;
    } // whitelistedTagsForPurifier
    
    /**
     * Return whitelisted tags for editor
     * 
     * @return string
     */
    static function getWhitelistedTagsForEditor() {
    	if (self::$whitelisted_tags === false) {
    		self::loadWhitelistedTags();
    	} // if
    	
    	if (is_foreachable(self::$whitelisted_tags)) {
    	  $result = array();
      	foreach (self::$whitelisted_tags as $whitelisted_tag => $whitelisted_attributes) {
      		$result[] = $whitelisted_tag . '[' . implode('|', $whitelisted_attributes) . ']';
      	} // foreach
      	
      	return implode(',', $result);
    	} else {
    		return '';
    	} // if
    } // whitelistedTagsForEditor
    
    /**
     * Convert raw text to rich text
     * 
     * @param string $raw_text
     * @param string $for
     * @return string
     */
    static function toRichText($raw_text, $for = null) {
    	if (!trim($raw_text)) {
    		return $raw_text;
    	} // if
    	
      $parser = SimpleHTMLDomForAngie::getInstance($raw_text);
      if ($parser === false) {
      	$parser = SimpleHTMLDomForAngie::getInstance(nl2br($raw_text));	
      } // if

      EventsManager::trigger('on_rawtext_to_richtext', array($parser, $for));
      $html = (string) $parser;
      
      return $html;
    } // toRichText
    
    /**
     * Clean up HTML code
     * 
     * @param string $html
     * @param Closure $extra_dom_manipulation
     * @return string
     */
    static function cleanUpHtml($html, $extra_dom_manipulation = null) {
      $html = trim($html);

      if ($html) {
        $html = preg_replace('/<img[^>]+src[\\s=\'"]+data\:(image\/.*)\;base64\,([^"\'>\\s]+)[^>]+>/is', '', $html); // strip raw embeded images
        $html = preg_replace('/<img[^>]+src[\\s=\'"]+webkit-fake-url\:\/\/[^"\'>\\s]+[^>]+>/is', '', $html); // strips images with webkit-fake-url://

        $html = HtmlPurifierForAngie::purify($html);
        
        $dom = SimpleHTMLDOMForAngie::getInstance($html);
  			if($dom) {
  			  // Remove Apple style class SPAN-s
  			  $elements = $dom->find('span[class=Apple-style-span]');
  			  if(is_foreachable($elements)) {
  			    foreach($elements as $element) {
  			      $element->outertext = $element->plaintext;
  			    } // foreach
  			  } // if

          // Remove empty paragraphs
          if(defined('REMOVE_EMPTY_PARAGRAPHS') && REMOVE_EMPTY_PARAGRAPHS) {
            foreach($dom->find('p') as $element) {
              $cleaned_up_content = trim(str_replace('&nbsp;', ' ', strip_tags($element->innertext)));

              // Empty paragraph (non-breaking spaces are converted to spaces so trim can remove them)?
              if(empty($cleaned_up_content)) {
                if(strpos($element->innertext, 'img')) {
                  continue;
                } // if

                $element->outertext = '';
              } // if
            } // foreach
          } // if

          if($extra_dom_manipulation instanceof Closure) {
            $extra_dom_manipulation->__invoke($dom);
          } // if

  			  $html = (string) $dom;
  			} // if
        
  			return $html;
      } else {
        return '';
      } // if
    } // cleanUpHtml

    /**
     * Strip links for anonnymous user
     *
     * @param string $html
     * @return string
     */
    static function stripLinksForAnonymousUser($html) {
      $parser = SimpleHTMLDomForAngie::getInstance($html);
      $anchors = $parser->find('a');
      if (is_foreachable($anchors)) {
        foreach ($anchors as $anchor) {
          if (strpos($anchor->class, 'public_link') === false) {
            $anchor->outertext = '<span style="' . $anchor->style . '">' . $anchor->innertext . '</span>';
          } // if
        } // foreach
      } // if
      return (string) $parser;
    } // stripLinksForAnonymousUser

    /**
     * Strips single tag from the string
     *
     * @param string $tag
     * @param string $html
     * @return string
     */

    static function stripSingleTag($tag,$html){
      $html=preg_replace('/<'.$tag.'[^>]*>/i', '', $html);
      $html=preg_replace('/<\/'.$tag.'>/i', '', $html);
      return $html;
    }

  } 