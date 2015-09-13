<?php

	// define system constants
	define('DS',            DIRECTORY_SEPARATOR);
	define('EOL',           PHP_EOL);

	// define file path constants
	define('BASE_DIR',      __DIR__);
	define('CACHE_DIR',     __DIR__.DS.'__cache');
	define('APP_DIR',       __DIR__.DS.'_app');
	define('ADMIN_DIR',     __DIR__.DS.'_admin');
	define('CONFIG_DIR',    __DIR__.DS.'_config');
	define('VIEWS_DIR',     __DIR__.DS.'_views');
	define('CONTENT_DIR',   __DIR__.DS.'content');

	define('PROJECTS_DIR',  CONTENT_DIR.DS.'projects');
	define('PAGES_DIR',     CONTENT_DIR.DS.'pages');


	// define cache busting mechanisms
	define('TIME_SYSTEM',   time());
	define('TIME_SECOND',   1);
	define('TIME_MINUTE', 	60 * TIME_SECOND);
	define('TIME_HOUR',     60 * TIME_MINUTE);
	define('TIME_DAY',      24 * TIME_HOUR);
	define('TIME_WEEK',     7 * TIME_DAY);


	// ---------------------------------------------------------------------------


	// get environment config
	$env = json_decode(file_get_contents(BASE_DIR.DS.'.env'), true);

	// define environment constants
	define('ENV_TIMEZONE',    $env['timezone']);
	define('ENV_CACHE_TIME',  $env['cacheTime']);


	// ---------------------------------------------------------------------------


	// define URL constants for public resources
	define('URL_SCRIPT',    'scripts/');
	define('URL_STYLE',     'styles/');
	define('URL_IMAGE',     'content/images/');


	// ---------------------------------------------------------------------------


	// define app timezone
	date_default_timezone_set(ENV_TIMEZONE);


	// ---------------------------------------------------------------------------


	// set debug mode if needed
	define('DEBUG_MODE', $env['dev']);


	// ---------------------------------------------------------------------------


	// TODO: Document on install that you must provide the root directory location against the domain root
	// TODO: Make this a config in the .env manifest
	// setup base URL
	define('BASE_URL', '/');


	// ---------------------------------------------------------------------------


	// setup the mime-types the app should filter
	$fileExtensions = require APP_DIR.DS.'mime-types.php';

	// serve the requested resource as-is.
	if (preg_match("/\.(?:".implode('|', $fileExtensions).")$/", preg_replace('/\?[\s\S]+/i', '', $_SERVER['REQUEST_URI']))) {
		return false;
	}


	// ---------------------------------------------------------------------------


	// autoload app dependencies
	require __DIR__.DS.'vendor'.DS.'autoload.php';


	// ---------------------------------------------------------------------------


	// load app
	require APP_DIR.DS.'app.php';

	// // load admin
	// require ADMIN_DIR.DS.'admin.php';

	// load routes loader
	require APP_DIR.DS."routes.php";


	// ---------------------------------------------------------------------------


	// run dispatcher
	dispatch();
