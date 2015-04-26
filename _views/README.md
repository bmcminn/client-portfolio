# Editing Templates/Themes

So you've got some extra requirements beyond what I put together for you. That's cool. Here's what you need to know.

## TL;DR

1. By default, the project view uses `photo.handlebars` as it's layout file. So any changes layout-wise would be helpful here.
2. The partials provided are licenses that allow you to templatize your license terms. Customize them as needed, or add your own custom ones. They're included via the `{{{markdown_partial license }}}` tag, where `license` is defined in the projects' `project.json`.
3. Once you make changes, they're immediately available when refresh your browser.

From here, you can upload your changes to your server using an FTP client of your choosing.


## Making Theme Changes

If you need to make theme changes, I highly suggest using the `resources/stylus` assets and process them by running `grunt` in your command line.

You can sub any CSS pre-processor you like via Grunt, or switch to Gulp if you like, but that's more work for you. Stylus supports most any [CSS format](https://learnboost.github.io/stylus/docs/css-style.html) right out of the box, so just get to work.
