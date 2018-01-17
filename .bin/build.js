

// process.env.NODE_ENV = false;


const path      = require('path');
const fs        = require('grunt').file;
const chokidar  = require('chokidar');
const _         = require('lodash');
const Stylus    = require('stylus');
const CSSO      = require('csso')
const chalk     = require('chalk')


var Log     = console.log.bind(console);
var Debug   = console.debug.bind(console);
var Error   = console.error.bind(console);


let RSRC_DIR    = path.join(process.cwd(), 'resources');
let STYL_DIR    = path.join(RSRC_DIR, 'styl');
let JS_DIR      = path.join(RSRC_DIR, 'js');

let DIST_DIR    = path.join(process.cwd(), 'public');
let CSS_DIST    = path.join(DIST_DIR, 'css');
let JS_DIST     = path.join(DIST_DIR, 'js');


compileStyles('get-this-party-started');
compileJS('/');


let watchFiles = [].concat(
    fs.expand({ filter: 'isFile'}, path.join(RSRC_DIR, '/**/*.styl'))
,   fs.expand({ filter: 'isFile'}, path.join(RSRC_DIR, '/**/*.js'))
);


Log(watchFiles);

chokidar
    .watch(watchFiles, {ignored: /(^|[\/\\])\../})
    .on('any', (e, filepath) => {
        Log(filepath);

    })
    .on('change', (filepath, filemeta) => {


        // skip no stylus files
        if (filepath.match(/\.styl$/)) { compileStyles(); }
        if (filepath.match(/\.js$/)) { compileJS(filepath); }

    })
    ;



// process.env.FILE_SERVER_PATH = './';
// process.env.FILE_SERVER_PORT = 8080;


// Log(`Starting node fileserver at http://localhost:${process.env.FILE_SERVER_PORT}`);
// require('node-file-server');


function compileStyles() {

    let styles = fs.expand({ filter: 'isFile' }, [
            path.join(STYL_DIR, '**/*')
        ,   "!"+path.join(STYL_DIR, '**/_*')
        ]);

    _.each(styles, function(style) {
        let filename = path.basename(style)
                .replace(/\s+/, '-')
                .toLowerCase()
            ;

        let newStyle = path.join(CSS_DIST, filename.replace(/\.[\w\d]+/, ''));

        let content = fs.read(style);


        Stylus(content)
            .set('filename',    style)
            .set('paths',       [ STYL_DIR ])
            // .set('linenos',     process.env.NODE_ENV ? false : true)
            // .set('compress',    process.env.NODE_ENV ? true : false)
            .render(function(err, css) {

                if (err) {
                    console.error(chalk.red(err));
                    return;
                }

                // POST PROCESS CSS A BIT
                css = css
                    .replace(/#__ROOT__/gi, ':root')
                    .replace(/PP__/gi, '--')
                    ;

                // Write unminified styles to disk
                fs.write(`${newStyle}.css`, css);

                let csso_opts = {
                    debug:  process.env.NODE_ENV ? false : true
                // ,   c:      process.env.NODE_ENV ? true : false
                };

                css = CSSO.minify(css, csso_opts).css;

                // Log(css);
                fs.write(`${newStyle}.min.css`, css);

                Log(chalk.green(`> Compiled ${path.basename(style)}`));
            })
        ;

    });

}



function compileJS(filepath) {

    let files = [];

    if (filepath === '/') {
        files = fs.expand({ filter: 'isFile' }, [
            path.join(JS_DIR, '**/*')
        ]);

    } else {
        files.push(filepath);

    }

    _.each(files, function(src) {
        let filename = path.basename(src);
        let dest = path.join(JS_DIST, filename);
        fs.copy(src, dest);

        Log(chalk.green(`> Compiled ${filename}`));
    });
}
