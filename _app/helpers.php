<?php

    // "markdown" => function($content) {

    //   global $handlebarsConfig, $appModel;

    //   $markdown = new ParsedownExtra();

    //   $file = file_get_contents(VIEWS.DS.$content[0].HANDLEBARS_EXT);

    //   $template = LightnCandy::compile($file, $handlebarsConfig);
    //   $render   = LightnCandy::prepare($template);

    //   // echo $markdown->text($content[0]);
    //   return $markdown->text($render($appModel));
    //   // return "antskdsl";
    // }


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


  $handlebars->addHelper('markdown', function($template, $context, $args, $source) {
      global $appModel;

      $markdown = new ParsedownExtra();
      $handlebars = $template->getEngine();
      $template = $handlebars->render($context->get($args), $appModel);

      return $markdown->text($template);
    });
