
var stylus = {
			files: {
				'styles/main.css': '_assets/stylus/main.styl'
			, 'styles/legal.css': '_assets/stylus/legal.styl'
			, 'styles/about.css': '_assets/stylus/about.styl'
			, '_admin/styles/main.css': '_assets/stylus/admin.styl'
			}
		}
	;

module.exports = {
	dev: {
		options: {
			compress: false
		, linenos: true
		}
	, files: stylus.files
	}

, build: {
		options: {

		}
	, files: stylus.files
	}
};
