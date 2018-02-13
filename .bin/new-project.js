const q = require('inquirer');
const fs = require('grunt').file;
const path = require('path');
const utils = require('./utils-for-new.js');


// setup our questions array
const qs = [];


// -----------------------------------
qs.push({
    type: 'list'
,   name: 'client'
,   message: 'Client name:'
,   choices: [
        new q.Separator()
    ,   'new client'
    ,   new q.Separator()
    ].concat(utils.getClientDirs())
,   default: 0
,   pageSize: 10
});

    // -----------------------------------
    // If we chose a new client, enter client name and create directory
    qs.push({
        type: 'input'
    ,   name: 'newClientPath'
    ,   message: 'New client name:'
    ,   when: (answers) => answers.client.toLowerCase() === 'new client'
    ,   validate: (answer) => utils.validator('new client', answer)
    ,   default: null
    });


// -----------------------------------
qs.push({
    type: 'input'
,   name: 'title'
,   message: 'Project Title:'
,   validate: (answer) => answer !== ''
});


// -----------------------------------
qs.push({
    type: 'list'
,   name: 'contact'
,   message: 'Choose the primary contact:'
,   choices: [ { name: '---', 'value': null } ].concat(utils.getUsers())
,   pageSize: 10
});


// -----------------------------------
qs.push({
    type: 'list'
,   name: 'type'
,   message: 'Project Type:'
,   choices: [
        'photography'
    ,   'design'
    ,   'software'
    ,   'home decor'
    ]
});

    // -----------------------------------
    qs.push({
        type: 'list'
    ,   name: 'license'
    ,   message: 'Project Type:'
    ,   choices: [{ name: '---', value: null }].concat(utils.getLicenses())
    ,   when: (answers) => answers.type.match(/photography|design|software/i)
    ,   default: 'cc-by-sa'
    });





// // -----------------------------------
// qs.push({
//     type: 'checkbox',
//     name: 'folders',
//     message: 'Client folders:',
//     choices: () => utils.getClientDirs(),
//     when: (answers) => answers.type.toLowerCase() === 'client',
//     default: null
// });


// NOTE: might need some info denoting optional items
// // Setup UI with bottom bar
// var ui = new q.ui.BottomBar();

// ui.log.write('* - indicates an optional field');


// Init prompt
q.prompt(qs)
    .then(utils.writeProject);
