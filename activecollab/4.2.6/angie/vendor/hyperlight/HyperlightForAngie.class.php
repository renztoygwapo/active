<?php

  /**
   * Hyperlight for angie implementation
   * 
   * @package angie.vendor.hyperlight
   */
  final class HyperlightForAngie {
  	
		const SYNTAX_PLAIN = 'plain';
		const SYNTAX_C_PLUS_PLUS = 'cpp';
		const SYNTAX_C_SHARP = 'csharp';
		const SYNTAX_CSS = 'css'; 
  	const SYNTAX_PHP = 'iphp';
  	const SYNTAX_PYTHON = 'python';
  	const SYNTAX_VISUAL_BASIC = 'vb';
  	const SYNTAX_XML = 'xml';

    // lang("Plain Text"); Make sure that lang extractor captures this
  	
  	/**
  	 * Get list of languages supported with Hyperlight
  	 * 
  	 * @var array
  	 */
  	static private $languages = array(
      self::SYNTAX_PLAIN => array('Plain Text'),
  		self::SYNTAX_C_PLUS_PLUS => array ('C++'),
  		self::SYNTAX_C_SHARP => array('C#'),
  		self::SYNTAX_CSS => array('CSS'),
  		self::SYNTAX_PHP => array('PHP'),
  		self::SYNTAX_PYTHON => array('Python'),
  		self::SYNTAX_VISUAL_BASIC => array('Visual Basic'),
  		self::SYNTAX_XML => array('XML', 'HTML', 'XHTML'),
  	);

		/**
		 * Higlight $content with given $syntax
		 * 
		 * @param string $content
		 * @param string $syntax
		 * @return string
		 */
		static function higlight($content, $syntax) {
			$content = trim($content);
			$syntax = strtolower($syntax);
			if (in_array($syntax, self::getSyntaxes()) && ($syntax != self::SYNTAX_PLAIN)) {
				$hyperlight = new Hyperlight($syntax);
				return $hyperlight->render($content);
			} // if
			return clean($content);
		} // highlightSyntax
		
		/**
		 * Renders the full preview with line numbers and all necessary DOM
		 * 
		 * @param string $content
		 * @param string $syntax
     * @return string
		 */
		static function htmlPreview($content, $syntax) {
			$content = trim($content);
			$preview = trim(self::higlight($content, $syntax));

			if ($preview) {
        $number_of_lines = count(explode("\n", $content));

        $output = '<div class="syntax_higlighted source-code">';
        $output.= 	'<div class="syntax_higlighted_line_numbers lines"><pre>' . implode("\n", range(1, $number_of_lines)) . '</pre></div>';
        $output.=		'<div class="syntax_higlighted_source"><pre>' . $preview . '</pre></div>';
        $output.= '</div>';

        return $output;
      } else {
				return '';
			} // if
		} // htmlPreview
		
		/**
		 * Return list of available languages
		 * 
		 * @return array
		 */
		static function getAvailableLanguages() {
			return self::$languages;
		} // getAvailableLanguages
		
		/**
		 * Return available syntaxes
		 * 
		 * @return array
		 */
		static function getSyntaxes() {
			return array_keys(self::$languages);
		} // getSyntaxes
		
		/**
		 * Returns the syntax for given file based on file extension
		 * 
		 * @param string $filename
		 * @return string
		 */
		static function getSyntaxForFile($filename) {
			$extension = strtolower(get_file_extension($filename));
			
			switch ($extension) {
				case 'css':
					return self::SYNTAX_CSS;
					break;
					
				case 'html';
				case 'xml':
				case 'htm':
					return self::SYNTAX_XML;
					break;
				
				case 'c':
				case 'cpp':
				case 'h':
					return self::SYNTAX_C_PLUS_PLUS;
					break;
					
				case 'php':
					return self::SYNTAX_PHP;
					break;
				
				case 'vb':
					return self::SYNTAX_VISUAL_BASIC;
					break;
			} //switch
			
			return HyperlightForAngie::SYNTAX_PLAIN;
		} // getSyntaxForFile

  } // HyperlightForAngie