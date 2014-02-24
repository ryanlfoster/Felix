<?php

/**
 * Class Felix
 */

class Felix {
	private $content = array();
	private $plugins = array();

	private $query;
	private $twig;

	public function __construct() {
		$this->runHook('initialize');

		// Create Query instance
		$this->query = new Query();

		$this->runHook('post_query', array($this->query));
	}

	/* Predefined hooks */
	public function getPosts($opt) {
		// Overwrite default settings
		$settings = array_merge(array(
			'dir'       => '.',
			'exclude'   => array(),
			'limit'     => OPT_POST_LIMIT,
			'nobody'    => false,
			'offset'    => 0,
			'order'     => 'desc',
			'orderby'   => 'date',
			'ordertype' => 'date'
		), $opt);

		$settings['order'] = strtolower($settings['order']);

		$posts = array();

		// Index directory
		$files = $this->indexPosts($settings['dir']);

		// Iterate through posts
		foreach($files as $post) {
			// If one of these are in the 'exclude' array, don't add
			if(empty($post) || in_array($post, $settings['exclude'])) continue;

			// Add the post to the list
			array_push($posts, $this->getPost($settings['dir'] . '/' . $post, $settings['nobody']));
		}

		// Create a sort instance
		$sort = new Sort($settings['orderby'], $settings['ordertype']);

		// Is the order okay?
		if($settings['order'] === 'asc' || $settings['order'] === 'desc') {
			usort($posts, array($sort, 'sort'));
			if($settings['order'] === 'desc') array_reverse($posts);
		}

		return array_splice($posts, $settings['offset'], $settings['limit']);
	}

	public function getPost($name, $nobody = false) {
		// Create post instance
		$post = new Post($name, $nobody);

		$this->runHook('pre_parse_content', $post);

		// Format if necessary
		if(!$nobody) $post->format();

		$this->runHook('post_parse_content', $post);

		return $post;
	}

	private function indexPosts($dir) {
		// Open directory
		$dh = @opendir(DIR_CONTENT . '/' . $dir);
		$posts = array();

		if($dh) {
			// Iterate through directory entries
			while(($entry = readdir($dh)) !== false) {
				$len = strlen($entry);

				// Get before and after .md extension
				$pre = substr($entry, 0, $len - 3);
				$post = substr($entry, $len - 2);

				if($post !== 'md') continue;
				array_push($posts, $pre);
			}
		} else {
			$this->error('Can\'t read directory: ' . DIR_CONTENT . '/' . $dir);
		}

		return $posts;
	}

	/* Loading */
	public function load() {
		global $_VARS;

		$this->runHook('pre_load');

		// Do some loading
		$this->loadPlugins();
		$this->loadLibs();

		// Load the post
		$post = new Post($this->query->path);
		$post->format();

		// Get post variables
		$this->content['post'] = &$post;

		// Get site variables
		$this->content['site'] = &$_VARS;
		$this->content['site']['dir'] = DIR_ROOT;
		$this->content['site']['url'] = URI_ROOT;

		// Get (current) theme variables
		$this->content['theme'] = array(
			'dir'   => DIR_THEME,
			'name'  => OPT_THEME,
			'url'   => URI_THEME
		);

		$this->runHook('post_load', array($this->content));
	}

	private function loadLibs() {
		global $_TWIG;

		// get libraries
		require_once DIR_CORE . '/libs/Twig/Autoloader.php';
		require_once DIR_CORE . '/libs/markdown.php';

		// Load Twig
		Twig_Autoloader::register();

		$loader = new Twig_Loader_Filesystem(DIR_THEME);
		$this->twig = new Twig_Environment($loader, $_TWIG);

		// Initialize Twig
		$this->initializeTwig();

		$this->runHook('load_lib');
	}

	private function initializeTwig() {
		// Add default get_posts method
		$func = new Twig_SimpleFunction('get_posts', array($this, 'getPosts'));
		$this->twig->addFunction($func);

		// Add default get_post method
		$func = new Twig_SimpleFunction('get_post', array($this, 'getPost'));
		$this->twig->addFunction($func);

		// Provide the twig object
		$this->runHook('twig_init', array(&$this->twig));
	}

	private function loadPlugins() {
		if(is_dir(DIR_PLUGINS) && $dh = opendir(DIR_PLUGINS)) {
			while(($entry = readdir($dh)) !== false) {
				if($entry === '.' || $entry === '..') continue;

				$apath = DIR_PLUGINS . '/' . $entry;
				$is_dir = is_dir($apath);

				if(!$is_dir) $entry = substr($entry, 0, strpos($entry, '.'));

				$classname = preg_replace('/[^0-9a-z]/i', ' ', strtolower($entry));
				$classname = str_replace(' ', '', ucwords($classname));

				if($is_dir) {
					$entry .= '/' . $entry;
					$apath .= '/' . $entry;
				}

				if(file_exists($apath)) require_once $apath;
				if(class_exists($classname)) $this->plugins[$entry] = new $classname;
			}
		}

		$this->runHook('plugin_init');
	}

	/* Rendering */
	public function render() {
		$post = $this->content['post'];

		if(!isset($post->template)) $template = 'index';
		else $template = $post->template;

		try {
			$template = $this->twig->loadTemplate($template . '.html');

			$this->runHook('pre_render');
			echo $template->render($this->content);
			$this->runHook('post_render');
		} catch(Twig_Error $e) {
			$this->error($e->getMessage());
		}
	}

	/* Hooking */
	public function runHook($hook, $args = array()) {
		$returns = array();

		foreach($this->plugins as $plugin) {
			$ref = array($plugin, $hook);

			if(!is_callable($ref)) continue;
			array_push($returns, call_user_func_array($ref, $args));
		}

		return $returns;
	}

	/* Misc */
	public function error($msg) {
		$responses = $this->runHook('error');

		if(!in_array(false, $responses)) {
			if(!OPT_DEBUG) return;
			throw new Exception($msg);
		}
	}

	/* Static */

	public static function initialize() {
		return new Felix();
	}
}
