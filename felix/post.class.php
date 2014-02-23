<?php

/**
 * Class Post
 */

class Post {
	public $date;
	public $content;
	public $meta;
	public $template;
	public $title;
	public $url;

	public function __construct($page, $nobody = false) {
		$file = DIR_CONTENT . '/' . $page . '.md';

		// Serve 404 page if specified file doesn't exist
		if(!file_exists($file)) $file = DIR_CONTENT . '/404.md';

		$buffer = '';
		$done = false;

		$fp = fopen($file, 'r');

		// Such a performance-safer
		while(!feof($fp) && !$done) {
			$buffer .= fread($fp, 1);

			if(empty($header)) {
				$len = strlen($buffer);
				$last = substr($buffer, max($len - 2, 0), 2);

				// Looks like the beginning of a header
				if( $last === '/*') $buffer = '';

				// Looks like the end of a header
				if($last === '*/') {
					// If we don't need the body, we're done here
					if($nobody) $done = true;

					// Get the headers out of the buffer
					$header = trim(substr($buffer, 0, $len - 2));
					$this->meta = $this->parseHeaders($header);
					$buffer = '';
				}
			}
		}

		fclose($fp);

		// Our content is whatever that remains in the buffer
		$this->content = $buffer;

		// Change some meta to properties
		$meta = array('date', 'template', 'title');

		// Convert some meta tags to regular fields
		foreach($meta as $m) {
			$this->$m = isset($this->meta[$m]) ? $this->meta[$m] : null;
			unset($this->meta[$m]);
		}

		// If we're serving the index page, the URL is probably the root URL
		if($page === 'index' || $page === './index') $this->url = URI_ROOT;
		else $this->url = URI_ROOT . '/' . $page;
	}

	public function format() {
		// Prefix absolute URL's
		$this->content = preg_replace('/\[(.+?)\]\s?\(\/?(.+?)\)/', '[$1](' . URI_ROOT . '/$2)', $this->content);
		$this->content = Markdown($this->content);
	}

	private function parseHeaders($raw) {
		// We need to read this per line
		$raw = preg_split('/\r\n|\n\r|\r|\n/', $raw);
		$headers = array();

		// Iterate through each line
		foreach($raw as $h) {
			// If the line is empty or doesn't has a colon, it's probably not a header
			if(empty($h) || !strstr($h, ':')) continue;

			// Lets extract what we want
			list($key, $value) = explode(':', $h, 2);
			$key = trim(strtolower($key));
			$value = trim($value);

			// Save the header as key => value
			$headers[$key] = $value;
		}

		return $headers;
	}
}
