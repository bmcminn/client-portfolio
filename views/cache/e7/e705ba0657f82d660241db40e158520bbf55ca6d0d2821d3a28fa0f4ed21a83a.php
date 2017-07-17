<?php

/* login.twig */
class __TwigTemplate_15e80de5192aa02b4b348a072af2c9ea5472afd4d10488e95447a43e2a82e5cb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig", "login.twig", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_content($context, array $blocks = array())
    {
        // line 5
        echo "<form class=\"box\" action=\"";
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["routes"] ?? null), "home", array()), "html", null, true);
        echo "\" method=\"post\">


    <div class=\"field\">
        <label class=\"label\">Username</label>
        <p class=\"control\">
            <input class=\"input\" type=\"text\" placeholder=\"Username\" name=\"username\">
        </p>
    </div>

    <div class=\"field\">
    <label class=\"label\">Password</label>
        <p class=\"control\">
            <input class=\"input\" type=\"password\" placeholder=\"Password\" name=\"password\">
        </p>
    </div>


    <div class=\"field is-grouped\">
        <p class=\"control\">
            <button class=\"button is-primary\">Submit</button>
        </p>

        <p class=\"control\">
            <button class=\"button is-link\">Forgot Password?</button>
        </p>
    </div>


</form>
";
    }

    public function getTemplateName()
    {
        return "login.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  31 => 5,  28 => 4,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "login.twig", "C:\\Users\\Brandtley\\github\\client-portfolio\\app\\views\\login.twig");
    }
}
