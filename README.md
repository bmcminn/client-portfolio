# Client Portfolio

> Version: 0.0.1


## Overview


## Features


## Workflow


## Deploying


## Changelog

Changes are logged as best as possible, but are not guaranteed to be in order of application. However the date of each log entry will be accurate.

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
