const path = require('path');
const fs = require('grunt').file;
const YAML = require('js-yaml');
const chalk = require('chalk');
const leven = require('leven');

// Define regex constants
const regex = {};

regex.email = /\S+@\S+\.\S{2,5}/i



const paths = {};

paths.users     = path.join(process.cwd(), 'users');
paths.clients   = path.join(process.cwd(), 'clients');



const utils = {};

/**
 * [validator description]
 * @param  {[type]} type [description]
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
utils.validator = function(type, data) {
    let res = false;

    switch(type.toLowerCase()) {
        case 'email':
            res = regex.email.test(data.trim());
            break;

        // should return true if the client directory does not exist
        case 'new client':
            res = !utils.clientDirExists(data);
            break;

        default:
            console.error(`[ERROR] '${type}' is not valid validation method.`);
            return false;
    }

    return res;
}



utils.clientDirExists = function(clientName) {

    // console.log('[clientDirExists] start:', clientName);

    clientDirName = clientName
        .trim()                 //
        .toLowerCase()
        .replace(/\s+/g, '-')   // hyphenate the string
        ;

    // console.log('[clientDirExists] hyphenated:', clientName);

    let res = false;

    let clientDirs = utils.getClientDirs();

    clientDirs.map((dir) => {

        // console.log('[clientDirExists] dir check:', clientName);
        let targetDir = `clients/${clientDirName}`;

        if (dir.value === targetDir) {
            console.error(
                chalk.red('\n\n> [ERROR]'), `Directory ${chalk.cyan(`'${targetDir}'`)} already exists; choose another client name.`
            ,   '\n'
            );
            res = true;
            return;
        }

        // fuzzy check the string to see if the user may have goofed the name
        if (leven(dir.value, targetDir) < 3) {
            console.log(
                chalk.yellow('\n\n> [WARN]'), `That client may already exist ${chalk.cyan(`'${dir.value}'`)}`
            ,   chalk.yellow('\n> [WARN]'), `If you meant ${chalk.cyan(dir.name)}, cancel this process by hitting ${chalk.bold.white('Ctrl+C')}`
            ,   chalk.yellow('\n> [WARN]'), `    and select ${chalk.cyan(dir.name)} from the list of existing clients.`
            ,   '\n'
            );
        }

        // console.log('[DEBUG: clientDirExists] leven result', leven(dir.value, targetDir));

        // fuzzy check the string with the target path
    });

    // console.log('[clientDirExists] post clientDirs check:', res);

    return res;
};



/**
 * Gets list of directories in the clients/ directory
 * @return {collection} List of client folders
 */
utils.getClientDirs = function() {
    // get all client Dirs
    let clientDirs = [];

    let dirs = fs.expand({ filter: 'isDirectory' }, [
        path.resolve(paths.clients, '*')
    ]);

    dirs.map((folder) => {
        clientDirs.push({
            name: path.basename(folder)
        ,   value: folder.substr(process.cwd().length + 1)
        });
    });

    return clientDirs;
};


/**
 * Gets all users defined in the system
 * @return {[type]} [description]
 */
utils.getUsers = function() {
    // get all client Dirs
    let users = [];

    let userFiles = fs.expand({ filter: 'isFile' }, [
        path.resolve(paths.users, '*')
    ]);

    userFiles.map((userFile) => {
        let user = fs.readYAML(userFile);

        if (user.type === 'admin') { return; }

        users.push({
            name: `${user.fullname} -- ${user.email}`
        ,   value: userFile.substr(process.cwd().length + 1)
        });
    });

    return users;
}



/**
 * Return a list of licenses to choose from when creating a project
 * @info: https://choosealicense.com/
 * @return {array} The list of licenses we can choose from
 */
utils.getLicenses = function() {


    licenses = [
    ,   {
            name: 'Attribution Only -- BY'
        ,   value: 'cc-by'
        }
    ,   {
            name: 'Attribution, Share Alike -- BY SA'
        ,   value: 'cc-by-sa'
        }
    ,   {
            name: 'Attribution, No Derivatives -- BY ND'
        ,   value: 'cc-by-nd'
        }
    ,   {
            name: 'Attribution, Non-Commercial -- BY NC'
        ,   value: 'cc-by-nc'
        }
    ,   {
            name: 'Attribution, Non-Commercial, and Share Alike -- BY NC SA'
        ,   value: 'cc-by-nc-sa'
        }
    ,   {
            name: 'Attribution, Non-Commercial, and No Derivatives -- BY NC ND'
        ,   value: 'cc-by-nc-nd'
        }
    ,   {
            name: 'Public Domain -- CC0'
        ,   value: 'cc-0'
        }
    ,   {
            name: 'MIT'
        ,   value: 'mit'
        }
    ,   {
            name: 'MIT - Beerware'
        ,   value: 'mit-beerware'
        }
    ,   {
            name: 'BSD'
        ,   value: 'mit-beerware'
        }
    ,   {
            name: 'GPL'
        ,   value: 'mit-beerware'
        }

    ]


    return licenses;
};



/**
 * Writes object/array data to YAML filepath specified
 * @param  {string}     writePath   path the file wants to be written to
 * @param  {array/obj}  data        Data to be written to the YAML file
 * @return {null}
 */
utils.writeYAML = function(writePath, data) {
    let content = [
        '---'
    ,   YAML.safeDump(data)
    ].join('\n');

    fs.write(writePath, content);
};



/**
 * Writes user config to users directory
 * @param  {[type]} user [description]
 * @return {[type]}      [description]
 */
utils.writeUser = function(user) {
    let filename = user.fullname
        .trim()
        .toLowerCase()
        .replace(/\s+?/gi, '-')
        ;

    console.log(paths.users);

    let writePath = path.join(paths.users, `${filename}.yaml`);

    utils.writeYAML(writePath, user);
}


/**
 * Writes project config to projects directory
 * @param  {[type]} project [description]
 * @return {[type]}      [description]
 */
utils.writeProject = function(project) {
    project.dir     = project.client + '/' + utils.hyphenate(project.title);
    project.client  = path.basename(project.client);

    let writePath = path.join(process.cwd(), project.dir, `project.yaml`);

    utils.writeYAML(writePath, project);
}


utils.hyphenate = function(str) {
    return str
        .trim()
        .toLowerCase()
        .replace(/\s+?/gi, '-')
        .replace(/-{2,}/g, '--')
        ;
}



// export the utils object
module.exports = utils;
