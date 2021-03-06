<?php

$dirs = array('plugins');
$files = array('.htaccess', 'config.php');

$dir_current = dirname(__FILE__);
$dir_install = $dir_current . '/install';

if(!is_dir($dir_install)) {
	die('Both your <code>config.php</code> and <code>install</code> directory seem to be missing. You may want to reinstall Felix entirely.');
} else if(!is_writable($dir_install)) {
	die('Please add writing permissions to the <code>install</code> directory. It will be deleted after completion.');
} else {
	if(!is_writable($dir_current)) {
		die('Please add writing permissions to the root directory. You can change it back when no messages pop up.');
	} else {
		$path_root = $_SERVER['REQUEST_URI'];
		if(substr($path_root, strlen($path_root) - 1) !== '/') $path_root .= '/';

		// Gather data for URI_ROOT
		$protocol = isset($_SERVER['HTTPS']) ? 'https' : 'http';
		$uri_root = sprintf('%s://%s', $protocol, $_SERVER['SERVER_NAME']);
		$uri_root .= (intval($_SERVER['SERVER_PORT']) !== 80) ? ':' . $_SERVER['SERVER_PORT'] : null;
		$uri_root .= rtrim($path_root, '/');

		/* Gather data it needs */
		$data = array(
			'__URI_ROOT__'  => $uri_root,
			'__PATH_ROOT__' => $path_root
		);

		foreach($dirs as $dir) {
			$adir = $dir_current . '/' . $dir;

			if(is_dir($adir)) continue;
			mkdir($adir);
		}

		foreach($files as $file) {
			$oldfile = $dir_install . '/' . $file;
			$newfile = $dir_current . '/' . $file;

			// Get file content
			$content = file_get_contents($oldfile);

			// Loop through variables
			foreach($data as $key => $value) {
				$content = str_replace($key, $value, $content);
			}

			// Save file to new location
			file_put_contents($newfile, $content);
			@unlink($oldfile);
		}

		// Delete install directory
		@rmdir($dir_install);
	}
}

// Goodbye world!
unlink($dir_current . '/install.php');
