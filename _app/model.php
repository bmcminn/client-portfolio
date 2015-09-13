<?php

	// setup $model
	$model = [];


	// set location for model.cache
	$cacheFile = CACHE_DIR.DS.'model.cache';


	// get model cache
	if (!DEBUG_MODE && file_exists($cacheFile)) {

		// is the model cache still valid?
		if (time_to_bust_cache($cacheFile)) {
			$model['cache'] = true;
		}
	}


	// if our model cache is valid
	if (isset($model['cache'])) {

		// retrieve the cached model data
		$model = get_json($cacheFile);

	// process the model if cache is out of date
	} else {

		$model = get_directory_files(CONFIG_DIR, 'yaml');

		// write model cache file
		put_json($cacheFile, $model);
		// file_put_contents($cacheFile, json_encode($model));

	}


	// define if we're on a secure server or not
	$http       = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'http';
	$serverName = $_SERVER['HTTP_HOST'];


	// define more specific site properties
	$model['site']['url'] = "{$http}://{$serverName}".BASE_URL;

	$model['debugMode'] = DEBUG_MODE;

	// define the global 'model' object
	config('model', $model);
