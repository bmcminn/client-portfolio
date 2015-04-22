<?php

  return [

    /**
     *
     *
     */
    "markdown" => function($content) {

      // TODO: add in markdown parsing
      global $handlebarsConfig, $appModel;

      $markdown = new ParsedownExtra();

      $file = file_get_contents(VIEWS.DS.$content[0].HANDLEBARS_EXT);

      $template = LightnCandy::compile($file, $handlebarsConfig);
      $render   = LightnCandy::prepare($template);

      // echo $markdown->text($content[0]);
      return $markdown->text($render($appModel));
      // return "antskdsl";
    }

  ];
