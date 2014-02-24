<?php

global $_VARS, $_TWIG;

/* General options */
define('OPT_THEME', 'felix');
define('OPT_POST_LIMIT', 10);
define('OPT_DEBUG', true);

/* Variables which can be used in the templates */
$_VARS = array(
	'title'         => 'Felix',
	'description'   => 'A flat-file CMS aiming on performance and flexibility.'
);

/* Twig settings */
$_TWIG = array(
	'debug'         => OPT_DEBUG,
	'autoescape'    => false
);

/* URI's */
define('URI_ROOT', '__URI_ROOT__');
define('URI_THEMES', URI_ROOT . '/themes');
define('URI_THEME', URI_THEMES . '/' . OPT_THEME);

/* Directories */
define('DIR_ROOT', dirname(__FILE__));
define('DIR_CORE', DIR_ROOT . '/felix');
define('DIR_CONTENT', DIR_ROOT . '/content');
define('DIR_PLUGINS', DIR_ROOT . '/plugins');
define('DIR_THEMES', DIR_ROOT . '/themes');
define('DIR_THEME', DIR_THEMES . '/' . OPT_THEME);
