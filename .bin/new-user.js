const q = require('inquirer');
const fs = require('grunt').file;
const path = require('path');
const utils = require('./utils-for-new.js');


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
    // validate: (answers) => answers.trim().match(regex.email),
    default: 'client'
});


// Question: user full name
// -----------------------------------
qs.push({
    type: 'input',
    name: 'fullname',
    message: 'User full name:',
    // validate: (answers) => answers.trim().match(regex.email),
    default: null
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'input',
    name: 'email',
    message: 'User email:',
    validate: (answer) => utils.validator('email', answer)
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'input',
    name: 'phone',
    message: 'User phone (optional):',
    // validate: (answers) => regex.email.test(answers.trim())
    default: ''
});


// Question: user email address
// -----------------------------------
qs.push({
    type: 'checkbox',
    name: 'folders',
    message: 'Client folders:',
    choices: () => utils.getClientDirs(),
    when: (answers) => answers.type.toLowerCase() === 'client',
    default: null
});


// NOTE: might need some info denoting optional items
// // Setup UI with bottom bar
// var ui = new q.ui.BottomBar();

// ui.log.write('* - indicates an optional field');


// Init prompt
q.prompt(qs)
    .then(utils.writeUser);
