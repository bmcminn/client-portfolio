
var files = {
			'scripts/main.min.js': '_assets/js/main.js'
		, 'scripts/libs/jquery.min.js': '_assets/js/libs/jquery.js'
		, 'scripts/libs/jquery.scrollNav.min.js': '_assets/js/libs/jquery.scrollNav.js'
		, 'scripts/libs/jquery.scrollTo.min.js': '_assets/js/libs/jquery.scrollTo.js'
		, 'scripts/libs/jquery.yalb.min.js': '_assets/js/libs/jquery.yalb.js'
		, 'scripts/libs/modernizr.min.js': '_assets/js/libs/modernizr.js'

		, 'scripts/main.js': '_assets/js/admin.js'
		}
	;


module.exports = {

	dev: {
		options: {
			mangle: false
		, compress: false
		, beautify: true
		, preserveComments: true
		}
	, files: files
	}

, build: {
		options: {
			mangle: true
		, report: 'gzip'
		, compress: true
		, beautify: false
		, preserveComments: false
		// , screwIE8: true
		// , mangleProperties: true
		}
	, files: files
	}

};
