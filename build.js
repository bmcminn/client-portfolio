'use strict';

require('dotenv-safe').load();


if (process.env.NODE_ENV === 'production') {
    process.env.HOST = process.env.PROD_HOST;

} else {
    process.env.HOST = process.env.PROD_HOST + ':' + process.env.PORT;

}


const fs        = require('grunt').file;
fs.stats        = require('fs').statSync;
fs.defaultEncoding = 'utf8';

const path      = require('path');
const exec      = require('child_process').exec;

const _         = require('lodash');
const chalk     = require('chalk');
const chokidar  = require('chokidar');

const SVGO      = require('svgo');
const svgo      = new SVGO({

});


const isProduction = process.env.NODE_ENV === 'production' ? true : false;



// Ensure public directory structures exist
const ASSET_DIRS = {
    CSS:    'css'
,   IMG:    'images'
,   JS:     'js'
,   FONT:   'fonts'
};

// Define paths for asset directories
const STATIC_DIR   = path.join(process.cwd(), 'static');
const ASSET_DIR    = path.join(process.cwd(), 'resources');


const CSS_SRC   = path.join(ASSET_DIR, ASSET_DIRS.CSS);
const CSS_DEST  = path.join(STATIC_DIR, ASSET_DIRS.CSS);

const JS_SRC   = path.join(ASSET_DIR, ASSET_DIRS.JS);
const JS_DEST  = path.join(STATIC_DIR, ASSET_DIRS.JS);

const IMG_SRC   = path.join(ASSET_DIR, ASSET_DIRS.IMG);
const IMG_DEST  = path.join(STATIC_DIR, ASSET_DIRS.IMG);

const FONT_SRC   = path.join(ASSET_DIR, ASSET_DIRS.FONT);
const FONT_DEST  = path.join(STATIC_DIR, ASSET_DIRS.FONT);


// Ensure each asset directory exists
_.each(ASSET_DIRS, function(pubDir, id) {
    let dir = path.join(STATIC_DIR, pubDir);

    if (!fs.exists(dir)) {
        fs.mkdir(dir);
    }

    dir = path.join(ASSET_DIR, pubDir);

    if (!fs.exists(dir)) {
        fs.mkdir(dir);
    }
});



// RUN INITIAL BUILD ON START UP
styles();
scripts();
images();
fonts();


chokidar
    .watch([
            ASSET_DIR   + '/**/*'
        // ,   CONTENT_DIR + '/**/*'
        ], {
            ignored: /(^|[\/\\])\../
        ,   persistent: true
        })


    .on('change', function(filepath, stats) {
        filepath = filepath.replace(/\\+/g, '/');

        let ext = path.extname(filepath);

        // if (ext.match(/\.(md)$/)) { updateContent(filepath, stats); }
        if (ext.match(/\.(eot|woff|woff2|ttf|otf)$/)) { fonts(); }
        if (ext.match(/\.(jpeg|jpg|png|gif|tiff)$/)) { images(); }
        if (ext.match(/\.(js)$/)) { scripts(); }
        if (ext.match(/\.(styl)$/)) { styles(); }
        if (ext.match(/\.(svg)$/)) { svg(); }

    })
;




//
// MIGRATE IMAGE ASSETS TO PUBLIC DIRECTORY
//

function images() {

    // TODO: leverage kraken.io API to optimize image assets for production
    //   -- https://www.npmjs.com/package/kraken

    let images = fs.expand({ filter: 'isFile' }, [
            path.join(IMG_SRC, '**/*')
        ]);

    _.each(images, function(filepath) {
        // console.log(filepath);
        let filename = filepath
                .replace(/\s+/gi, '-')
                .toLowerCase()
                .substr(path.join(IMG_SRC).length)
            ;

        let newImage = path.join(IMG_DEST, filename);

        fs.copy(filepath, newImage);
    });

    // console.log(chalk.green('migrated images'));
}



//
// MIGRATE IMAGE ASSETS TO PUBLIC DIRECTORY
//

function svg() {

    let svgs = fs.expand({ filter: 'isFile' }, [
            path.join(IMG_SRC, '**/*')
        ]);


    _.each(svgs, function(filepath) {

        let filename = filepath
                .replace(/\s+/gi, '-')
                .toLowerCase()
                .substr(path.join(IMG_SRC).length)
            ;

        let newFilepath = path.join(IMG_DEST, filename);

        let content = fs.read(filepath, { encoding: 'utf-8' });

        if (isProduction) {

            svgo
                .optimize(content, function(res) {
                    fs.write(newFilepath, res.data);
                })
            ;

        } else {
            fs.copy(filepath, newFilepath);

        }

    });

    // console.log(chalk.green('migrated SVGs'));
}



//
// MIGRATE FONT ASSETS
//

function fonts() {

    let fonts = fs.expand({ filter: 'isFile' }, [
            path.join(FONT_SRC, '**/*')
        ]);

    _.each(fonts, function(filepath) {
        // console.log(image);
        let filename = filepath
                .replace(/\s+/gi, '-')
                .toLowerCase()
                .substr(path.join(FONT_SRC).length)
            ;

        let newFont = path.join(FONT_DEST, filename);

        if (!fs.exists(newFont)) {
            fs.copy(filepath, newFont);
        }
    });

    console.log(chalk.green('migrated fonts'));

}



//
// COMPILE JS FILES
//

function scripts() {

    let Uglify = require('uglify-js');

    let scripts = fs.expand({ filter: 'isFile' }, [
            path.join(JS_SRC, '**/*')
        ]);

    _.each(scripts, function(script) {
        let filename = script
                .replace(/\s+/gi, '-')
                .toLowerCase()
                .substr(path.join(JS_SRC).length)
            ;

        let newImage = path.join(JS_DEST, filename);

        if (process.env.production) {
            let content = Uglify.minify(fs.read(script), {fromString: true});

            fs.write(newImage, content.code, { encoding: 'utf8' });

        } else {
            fs.copy(script, newImage);
        }

    });

    console.log(chalk.green('compiled JS'));

}



//
// COMPILE STYLES
//

function styles() {

    let Stylus = require('stylus');

    let styles = fs.expand({ filter: 'isFile' }, [
            path.join(CSS_SRC, '**/*')
        ,   "!" + path.join(CSS_SRC, '**/_*')
        ]);

    _.each(styles, function(style) {

        let filepath = style
            .substr(CSS_SRC.length)
            .replace(/\s+/, '-')
            .toLowerCase()
            ;

        let newStyle = path.join(CSS_DEST, filepath.replace(/\.[\w\d]+/, '.css'));

        let content = fs.read(style);

        Stylus(content)
            .set('filename',    style)
            .set('paths',       [ path.join(CSS_SRC, '/') ])
            .set('linenos',     process.env.NODE_ENV ? false : true)
            .set('compress',    process.env.NODE_ENV ? true : false)
            .render(function(err, css) {

                if (err) {
                    console.error(err);
                }

                // console.log(css);
                fs.write(newStyle, css);
            })
        ;

    });

    console.log(chalk.green('compiled styles'));

}
