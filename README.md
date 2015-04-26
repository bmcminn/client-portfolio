# Client Portfolio

> Version: 0.0.1


## Overview

A simple to use client project portal that allows you to send your client links to preview and/or download the work you develop for them.

### Features

- Easy install
- Flexible and extensible project setup and simple configuration
- Customize your layout and theme with [Handlebars](http://handlebarsjs.com/) and [Stylus](https://learnboost.github.io/stylus/)

### Folder structure (of a live app instance)

    |-- client-portfolio/
    |   |-- _app/..
    |   |-- _projects/
    |   |   |-- project-name/
    |   |   |   |-- # (png|jpg|jpeg|gif)'s go here
    |   |   |   |-- # zip's go here
    |   |   |   `-- project.json
    |   |   |-- other-projects/..
    |   |   `-- other-projects/..
    |   |-- _views/
    |   `-- vendors/
    |-- .htaccess
    `-- index.php

## Installation

1. Ensure you have the following installed:
    - Terminal application
    - [PHP 5.4+](https://php.net/downloads.php)
    - [Composer](https://getcomposer.org/download/)
    - [Node.js/NPM](https://nodejs.org/)
1. Enter the following commands to get up an running:
    ```bash
    > # install all PHP libraries
    > composer install

    > # install node.js packages
    > npm i

    > # If you're working from Localhost run the following and open in browser
    > php -S localhost:3005 index.php
    ```


## [Adding/Editing Projects](https://github.com/bmcminn/client-portfolio/tree/master/_projects/README.md)


## [Editing Templates/Themes](https://github.com/bmcminn/client-portfolio/tree/master/_views/README.md)


## Changelog

Changes are logged as best as possible, but are not guaranteed to be in order of application. However the date of each log entry will be accurate.

### April 26, 2015
- Updated documentation across repo.
- Updated Handlebars `helpers.php` to include a more robust markdown setup.
- Fixed the system so it works in deeper sub directory structures.
- Fixed the system so it works on shared hosts.
- Fixed the system so it allows you to request binary assets when developing in PHP's internal server instance.
- Added feature to handle multiple zip folders on projects.

### April 23, 2015

- Fixed error with Markdown parsing in Handlebars `{{markdown}}` helper.
- Added Handlebars helpers.
- Replaced LightnCandy library with [Xamin/Handlebars](https://github.com/XaminProject/handlebars.php).
- Rebuilt system to use [Dispatch](https://github.com/badphp/dispatch) for application routing.

### April 22, 2015

- Added config to composer to autoload certain application files.
- Added number of constant definitions to improve variable scope access.
- Added `gruntfile.js` and `.grunt/` folder for grunt related features.
- Added `package.json` including npm modules for building grunt process.
- Fixed `$appModel` merge to allow `project.json` to override `author.json` properties if needed.
- Added `README.md` to document the app and it's processes.
- Fixed issue with `markdown` helper not compiling Handlebars partial before running markdown parser.
- Added `markdown` helper to helpers.php for Handlebars.
- Added Markdown support via [Parsedown Extra](https://github.com/erusev/parsedown-extra).

### April 21, 2015

- Restructured app to centralize it in the parent/root directory.
- Initial build.
