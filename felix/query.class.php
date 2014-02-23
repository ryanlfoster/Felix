<?php

/**
 * Query
 * Wow
 *     such class
 * much code
 *   wow
 */

class Query {
	public $path;

	public function __construct() {
		$url = parse_url(URI_ROOT);

		// Lets get rid of the root path, if it's present
		if(!empty($url['path'])) $path = substr($_SERVER['REQUEST_URI'], strlen($url['path']));
		else $path = '';

		$this->path = trim($path, '/');

		if(empty($this->path)) {
			$this->path = 'index';
		}
	}
}
