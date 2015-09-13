<?php

	use KzykHys\TwigExtensions\ExtensionAggregate;

	// initialize Twig template parser instance
	// $loader = new Twig_Loader_Filesystem(VIEWS_DIR);

	$twig_options = [
		'charset' => 'utf-8'
	, 'cache'   => DEBUG_MODE ? null : CACHE_DIR.DS.'cache'
	];


	$loader = new Twig_Loader_Filesystem(VIEWS_DIR);
	$twig = new Twig_Environment($loader, $twig_options);


	// Registers all extensions
	$twig->setExtensions(ExtensionAggregate::getExtensions());


	// define auto escape primitive
	$autoEscape = [
		'pre_escape'  => 'html'
	, 'is_safe'     => ['html']
	];


	// ===========================================================================


	//
	// TWIG FILTERS
	//


	/**
	 * Generates a  properly formatted stylesheet <link> tag
	 * @var [type]
	 * @example "$stylesheetPath"|stylesheet_tag
	 */
	$twig->addFilter(new Twig_SimpleFilter('stylesheet_tag', function($string) {
		$model  = config('model');
		$string = $model['site']['url'] . URL_STYLE . $string;
		return "<link rel=\"stylesheet\" href=\"{$string}\">";
	}, $autoEscape));



	/**
	 * Generates a properly formatted <script> source tag
	 * @var [type]
	 * @example "$scriptPath"|script_tag
	 */
	$twig->addFilter(new Twig_SimpleFilter('script_tag', function($string) {
		$model  = config('model');
		$string = $model['site']['url'] . URL_SCRIPT . $string;
		return "<script type=\"text/javascript\" src=\"{$string}\"></script>";
	}, $autoEscape));



	/**
	 * Generates a properly formatted <link> tag
	 * @var [type]
	 * @example "$faviconPath"|favicon($_faviconType)
	 */
	$twig->addFilter(new Twig_SimpleFilter('favicon', function($string, $args=null) {
		$model  = config('model');
		$string = preg_replace('/^\//', $model['site']['url'], $string);
		$rel    = 'shortcut icon';
		if ($args) { $rel=$args; }
		return "<link rel=\"{$rel}\" href=\"{$string}\">";
	}, $autoEscape));



	/**
	 * Generates an <img> tag with optional alt text
	 * @var [type]
	 * @example "$imagePath"|img_tag($_altText)
	 */
	$twig->addFilter(new Twig_SimpleFilter('image_tag', function($string, $args=null) {
		$model  = config('model');
		$string = preg_replace('/^\//', $model['site']['url'], $string);
		$alt    = $args ? $args : '';
		return "<img src=\"{$string}\" alt=\"{$alt}\">";
	}, $autoEscape));



	/**
	 * Generates a fully qualified URL string relative to the current site URL
	 * @var [type]
	 * @example "$urlString"|url
	 */
	$twig->addFilter(new Twig_SimpleFilter('url', function($string) {
		$model  = config('model');
		$string = preg_replace('/^\//', $model['site']['url'], $string);
		return $string;
	}));



	/**
	 * Generates an properly formatted tel: link tag
	 * @var [type]
	 * @example "$phoneString"|tel_link($_altText)
	 */
	$twig->addFilter(new Twig_SimpleFilter('tel_link', function($string) {
		return "<a rel=\"tel\" href=\"{$string}\">{$string}</a>";
	}, $autoEscape));



	/**
	 * Generates an properly formatted mailto: link tag
	 * @var [type]
	 * @example "$emailString"|mailto_link()
	 */
	$twig->addFilter(new Twig_SimpleFilter('mailto_link', function($string) {
		return "<a href=\"mailto:{$string}\">{$string}</a>";
	}, $autoEscape));



	/**
	 * Generates an properly formatted svg:use tag
	 * @var [type]
	 * @example "$svgIdString"|svg($classes)
	 */
	$twig->addFilter(new Twig_SimpleFilter('svg', function($string, $classes=null) {
		if ($classes) {
			$classes = " class=\"{$classes}\"";
		}
		return "<svg{$classes}><use xlink:href=\"{$string}\"></use></svg>";
	}, $autoEscape));



	/**
	 * Generates slugified string
	 * @var [type]
	 * @example "$string"|slugify()
	 */
	$twig->addFilter(new Twig_SimpleFilter('slugify', function($string) {
		return preg_replace('/\s/', '-', $string);
	}, $autoEscape));





	// ===========================================================================


	//
	// TWIG TAGS
	//


	// TODO: add custom tag for dump/json_preload


	// ===========================================================================


	/**
	 * [render description]
	 * @param  [type]
	 * @param  array
	 * @return [type]
	 */
	function render($template, $model=null) {
		global $twig;

		if (!$model) {
			$model = config('model');
		}

		$template .= $model['template']['ext'];

		// check if the file is a partial
		if (file_exists(VIEWS_DIR.DS.$template)) {
			// everythign is normal :D

		} elseif (file_exists(VIEWS_DIR.DS.'_'.$template)) {
			// we need to prepend an underscore!
			$template = '_'.$template;

		} else {
			// something is horribly wrong!!
			throw new Exception("{$template} does not exist.");

		}


		$template = $twig->render($template, $model);


		$regex = [
			// 'tabs'              => '/\t*/m'
		// , 'space'             => '/\s{2,}(\s\w)/m'
		  'space_after_tag'   => '/((?:title|div|header|footer|section|aside|a)>)\s{2,}(\s\w)/m'
		, 'space_before_tag'  => '/\s{2,}(<\/(?:title|div|header|footer|section|a>|aside))/m'
		];

		// $template = preg_replace($regex['space'], ' ', $template);
		$template = preg_replace($regex['space_after_tag'], '$1$2', $template);
		$template = preg_replace($regex['space_before_tag'], '$1', $template);
		// $template = preg_replace($regex['tabs'], '', $template);


		return $template;
	}
