<?php

/* layout.twig */
class __TwigTemplate_5ef7bfae588b58e8dadeb500dc3e6dac1ecf6c0b448ba9e122dfe649b355eaf6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html lang=\"en-us\">
<head>
    <meta charset=\"UTF-8\">

    ";
        // line 6
        if (twig_get_attribute($this->env, $this->getSourceContext(), ($context["page"] ?? null), "title", array())) {
            // line 7
            echo "        <title>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["page"] ?? null), "title", array()), "html", null, true);
            echo "</title>

    ";
        } else {
            // line 10
            echo "        <title>";
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["app"] ?? null), "title", array()), "html", null, true);
            echo "</title>

    ";
        }
        // line 13
        echo "
    <meta http-equiv=\"x-ua-compatible\" content=\"ie=edge\">
    <meta name=\"description\" content=\"";
        // line 15
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["app"] ?? null), "description", array()), "html", null, true);
        echo "\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">

    ";
        // line 19
        echo "    <link rel=\"apple-touch-icon\" href=\"icon.png\">
        ";
        // line 21
        echo "


    <link rel=\"stylesheet\" type=\"text/css\" href=\"https://cdnjs.cloudflare.com/ajax/libs/bulma/0.4.3/css/bulma.min.css\">

</head>
<body>

    <div class=\"\">
    ";
        // line 30
        $this->displayBlock('content', $context, $blocks);
        // line 32
        echo "    </div>

</body>
</html>
";
    }

    // line 30
    public function block_content($context, array $blocks = array())
    {
        // line 31
        echo "    ";
    }

    public function getTemplateName()
    {
        return "layout.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 31,  77 => 30,  69 => 32,  67 => 30,  56 => 21,  53 => 19,  47 => 15,  43 => 13,  36 => 10,  29 => 7,  27 => 6,  20 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "layout.twig", "C:\\Users\\Brandtley\\github\\client-portfolio\\app\\views\\layout.twig");
    }
}
