
const Stylus    = require('stylus');
const FS        = require('fs');
const Path      = require('path');


const STYLES_PATH   = Path.join(process.cwd(), 'resources/styl');
const OUTPUT_DIR    = Path.join(process.cwd(), 'resources/css');

const SRC_HTML      = Path.join(process.cwd(), '.cache/index.html');
const CSS_LINKS     = /href="\/([\s\S\/]+?\.css)"/gi;


let html    = FS.readFileSync(SRC_HTML).toString();
let styles  = html.match(CSS_LINKS);


styles.map((href) => {
    let srcStyle    = href.replace(CSS_LINKS, '$1');                // .css
    let filename    = Path.basename(srcStyle, '.css') + '.styl';    // .styl

    let filepath    = Path.join(STYLES_PATH, filename);             // .styl path
    let content     = FS.readFileSync(filepath).toString();         // .styl contents

    Stylus(content)
        .set('filename',    filepath)
        .set('paths',       [ STYLES_PATH ])
        .set('linenos',     process.env.NODE_ENV ? false : true)
        .set('compress',    process.env.NODE_ENV ? true : false)
        .render(function(err, css) {

            if (err) {
                console.error(err);
                // console.error(chalk.red(err));
                return;
            }

            // POST PROCESS CSS A BIT
            css = css
                .replace(/#__ROOT__/gi, ':root')
                .replace(/PP__/gi, '--')
                ;

            // let csso_opts = {
            //     debug:      process.env.NODE_ENV ? false : true
            // ,   compress:   true
            // // ,   compress:   process.env.NODE_ENV ? true : false
            // };

            // css = CSSO.minify(css, csso_opts).css;

            // console.log(css);
            FS.writeFileSync(`${srcStyle}`, css);

            console.log(`> Compiled ${Path.basename(filename)}`);
            // console.log(chalk.green(`> Compiled ${path.basename(style)}`));
        })
    ;

});

