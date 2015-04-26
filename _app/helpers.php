<?php


  $handlebars->addHelper('capitalize', function($template, $context, $args, $source) {
      return ucwords($context->get($args));
    });


  $handlebars->addHelper('upper', function($template, $context, $args, $source) {
      return strtoupper($context->get($args));
    });


  $handlebars->addHelper('lower', function($template, $context, $args, $source) {
      return strtolower($context->get($args));
    });

  // $handlebars->addHelper('url', function($template, $context, $args, $source) {
  //     return config('dispatch.url') . $context->get($args);
  //   });


  $handlebars->addHelper('markdown_template', function($template, $context, $args, $source) {
      global $appModel;

      $markdown = new ParsedownExtra();
      $handlebars = $template->getEngine();
      $template = $handlebars->render($context->get($args), $appModel);

      return $markdown->text($template);
    });


  $handlebars->addHelper('markdown_partial', function($template, $context, $args, $source) {
      global $appModel;

      $markdown = new ParsedownExtra();
      $handlebars = $template->getEngine();
      $template = $handlebars->render('_'.$context->get($args), $appModel);

      return $markdown->text($template);
    });


  $handlebars->addHelper('markdown', function($template, $context, $args, $source) {
      global $appModel;

      // TODO: alter this so we can grab a specific markdown files
      // $text = $context->get($args)
      $text = "";

      return $markdown->text($text);
    });
