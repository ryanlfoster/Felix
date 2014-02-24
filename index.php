<?php

global $felix;

if(!file_exists('config.php')) {
	require_once 'install.php';
}

// Yeah this is pretty important
require_once 'config.php';

// Many classes
require_once 'felix/felix.class.php';
require_once 'felix/post.class.php';
require_once 'felix/query.class.php';
require_once 'felix/sort.class.php';

// Rock 'n roll!
$felix = Felix::initialize();
$felix->load();
$felix->render();
