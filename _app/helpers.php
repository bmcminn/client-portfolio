<?php

  return [

    /**
     * Handlebars helper tag that acccepts the name of a partial and processes
     * it with handlebars, then processes it as markdown
     * @param  string $content partial name
     * @return string          handlebarred and markdowned content
     */
    "markdown" => function($content) {

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
