<?php

	use KzykHys\FrontMatter\FrontMatter;


	//
	// HOMEPAGE
	//
	map('GET', BASE_URL, function() {

		// get the model array
		$model = config('model');

		// get page data
		$file = PAGES_DIR.DS.'home.md';

		$pageData = FrontMatter::parse(file_get_contents($file));
		$template = $pageData->getConfig()['template'];

		$model['page'] = $pageData ? $pageData : $model['site'];

		// get projects models
		$model['page']['projects'] = get_projects(PROJECTS_DIR);
		// print_r($model);

		// render the page
		echo render($template, $model);
	});



	// ===========================================================================



	//
	// OTHER PAGES
	//
	map('GET', BASE_URL.'<id>', function($params) {
		// get the model array
		$model = config('model');
		$file  = PAGES_DIR.DS.$params['id'].'.md';


		if (file_exists($file)) {
			$frontMatter = FrontMatter::parse(file_get_contents($file));

			$model['page'] = $frontMatter->getConfig();
			$model['page']['content'] = $frontMatter->getContent();
			$model['page']['lastUpdated'] = filemtime($file);

		} else {
			error(404);
			return;

		}


		// render the page
		echo render($model['page']['template'], $model);
	});



	// ===========================================================================



	//
	// HTTP CODE PAGES
	//

	map([
		300, 301, 302, 303,
		400, 401, 403, 404,
		500, 501, 503, 504
	], function($code, $res=null) {

		// get the model
		$model    = config('model');
		$template = 'httpcode';

		// get page data
		$model['page'] = [
			'title' => $code
		];

		// render the page
		echo render($template, $model);

	});
