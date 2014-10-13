<?php 

	/**
	 * Returns all supported youtube domains
	 * 
	 * @return array
	 */
	function get_youtube_domains() {
		return array(
			'youtube.com',
			'youtube.co.uk',
			'youtube.br',
			'youtube.fr',
			'youtube.it',
			'youtube.jp',
			'youtube.nl',
			'youtube.pl',
			'youtube.es',
			'youtube.ie',
			'youtu.be'
		);
	} // get_youtube_domains

	/**
	 * Checks if provided url is valid youtube url (does not check if youtube video really exists)
	 * 
	 * @param string $url
	 * @return mixed
	 */
	function is_valid_youtube_url($url) {
		return get_youtube_video_id($url) === false ? false : true;
	} // is_valid_youtube_url
	
	/**
	 * Checks if provided url is valid youtube url (does not check if youtube video really exists)
	 * 
	 * @param string $url
	 * @return mixed
	 */
	function get_youtube_video_id($url) {
		$parsed = parse_url($url);

		$basedomain = strtolower(trim($parsed['host']));
		if (!$basedomain) {
			return false;
		} // if
		
		// extract basedomain
		$basedomain_parts = explode('.', $basedomain);
		$basedomain_parts_num = count($basedomain_parts);
		if ($basedomain_parts_num < 2) {
			return false;
		} // if
		
		// hack for co.uk domain
		if ($basedomain_parts[$basedomain_parts_num - 2] == 'co' && $basedomain_parts[$basedomain_parts_num - 1] == 'uk') {
			if ($basedomain_parts_num < 3) {
				return false;
			} // if
			$basedomain = $basedomain_parts[$basedomain_parts_num - 3] . '.' . $basedomain_parts[$basedomain_parts_num - 2] . '.' . $basedomain_parts[$basedomain_parts_num - 1];			
		} else {
			$basedomain = $basedomain_parts[$basedomain_parts_num - 2] . '.' . $basedomain_parts[$basedomain_parts_num - 1];
		} // if
		
		// domain is not in list of supported domains
		if (!in_array($basedomain, get_youtube_domains())) {
			return false;
		} // if
		
		// shortened url
		if ($basedomain == 'youtu.be') {
			$path = trim(array_var($parsed, 'path'));
			
			if (!$path || strlen($path) < 2) {
				return false;
			} // if
			
			$video_id = trim(substr($path, 1));
			if (!$video_id) {
				return  false;
			} // if
			
			return $video_id;
		} else {
			$query = trim($parsed['query']);
			parse_str($query, $parsed_query);
			$video_id = array_var($parsed_query, 'v', null);
			if (!$video_id) {
				return false;
			} // if
			return $video_id;
		} // if
	} // get_youtube_video_id