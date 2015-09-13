<?php

	use Symfony\Component\Yaml\Yaml;


	/**
	 * Retrieves all config files of a given type from a flat directory
	 * @param  string   $location Absolute file URL of the directory
	 * @param  string   $ext      File extension for glob pattern
	 * @param  (array)  $ext      File extensions for glob pattern
	 * @return array              System model data
	 */
	function get_directory_files($location, $ext) {

		$model = [];

		// make sure the extension isn't a number
		if (is_numeric($ext)) {
			throw new Exception("GTFO! Don't be putting numbers in the \$ext value...");
		}

		// if the extension is an array, convert to glob brackets string
		if (is_array($ext)) {
			$ext = '{' . implode(',', $ext) . '}';
		}


		// collect and parse all configuration files
		$configFiles = glob($location . DS . '*.' . $ext, GLOB_BRACE);

		foreach ($configFiles as $key => $value) {

			$value = explode(DS, $value);
			$value = $value[count($value)-1];
			$value = preg_replace('/\.[\s\S]*/', '', $value);

			$model[$value]  = Yaml::parse(file_get_contents($location.DS.$value.'.'.$ext));

		}

		return $model;
	}


	// ===========================================================================


	/**
	 * [get_json description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	function get_json($file) {

		$regex = [
		  'block_comment' => '/\/\*+[\s\S]*\*\//'
		, 'line_comment'  => '/\/+[\s\S]*?(\r|\n)/'
		];

		$json = file_get_contents($file);

		foreach ($regex as $key => $reg) {
			$json = preg_replace($reg, '', $json);
		}

		return json_decode($json, true);

	}


	// ===========================================================================


	/**
	 * [put_json description]
	 * @param  [type] $file [description]
	 * @return [type]       [description]
	 */
	function put_json($file, $data, $pretty=false) {

		// pretty print the file?
		if ($pretty) {
			file_put_contents($file, json_encode($data), JSON_PRETTY_PRINT);

		// dump the minified file
		} else {
			file_put_contents($file, json_encode($data));
		}

	}



	// ===========================================================================


	/**
	 * Determines if the target files modtime is greater than the time to cache value in .env
	 * @param  (string) $cachedFile Absolute system path of file to check the modtime
	 * @return (bool)
	 */
	function time_to_bust_cache($cachedFile) {
		if (filemtime($cachedFile) >= (TIME_SYSTEM - (ENV_CACHE_TIME * TIME_HOUR))) {
			return true;
		} else {
			return false;
		}
	}



	// ===========================================================================



	/**
	 * [get_projects description]
	 * @return [type] [description]
	 */
	function get_projects() {
		return [];
	}
