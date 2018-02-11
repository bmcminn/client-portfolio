const q = require('inquirer');
const fs = require('grunt').file;
const path = require('path');
const YAML = require('js-yaml');

// Define regex constants
const regex = {};

regex.email = /\S+@\S+\.\S{2,5}/i


// setup our questions array
const qs = [];


// Question: user full name
// -----------------------------------
qs.push({
    type: 'list',
    name: 'type',
    message: 'User type:',
    choices: [
        'admin',
        'client',
    ],
    default: 'client',
    // validate: (answer) => answer.trim().match(regex.email),
});


// Question: user full name
// -----------------------------------
qs.push({
    type: 'input',
    name: 'fullname',
    message: 'User full name:',
    // validate: (answer) => answer.trim().match(regex.email),
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'input',
    name: 'email',
    message: 'User email:',
    validate: (answer) => regex.email.test(answer.trim())
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'input',
    name: 'phone',
    message: 'User phone (optional):',
    // validate: (answer) => regex.email.test(answer.trim())
});


// get all client folders
let clientFolderChoices = [];

let folders = fs.expand({ filter: 'isDirectory' }, [
    path.resolve(process.cwd(), 'clients/*')
]);

folders.map((folder) => {
    clientFolderChoices.push({
        name: path.basename(folder),
        value: folder,
    })
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'checkbox',
    name: 'folders',
    message: 'Client folders:',
    choices: clientFolderChoices,
    when: (answers) => answers.type.toLowerCase() !== 'client',
});


// NOTE: might need some info denoting optional items
// // Setup UI with bottom bar
// var ui = new q.ui.BottomBar();

// ui.log.write('* - indicates an optional field');


// Init prompt
q.prompt(qs)
    .then((user) => {
        let filename = user.fullname
            .toLowerCase()
            .replace(/\s+?/gi, '-')
            ;

        // update user folder paths to be relative to project root
        user.folders = user.folders.map((folder) => folder.substr(process.cwd().length + 1));

        let content = [
            '---',
            YAML.safeDump(user)
        ].join('\n');

        let writePath = path.join(process.cwd(), 'users', `${filename}.yaml`);

        fs.write(writePath, content);
    });
