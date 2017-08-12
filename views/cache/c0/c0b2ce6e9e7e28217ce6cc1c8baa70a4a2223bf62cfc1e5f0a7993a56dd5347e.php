<?php

/* register.twig */
class __TwigTemplate_605d4119c0d84a723fbbc38adec59060f022f0a8b1f50f2e1ea5f7e18770f8fa extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig", "register.twig", 1);
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
        echo "
<h1 class=\"h1\">Register Client</h1>

<form class=\"box\" action=\"";
        // line 8
        echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), ($context["routes"] ?? null), "home", array()), "html", null, true);
        echo "\" method=\"post\">


    ";
        // line 11
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["inputs"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["input"]) {
            // line 12
            echo "        <div class=\"field\">
            <label class=\"label\">";
            // line 13
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "label", array()), "html", null, true);
            echo "</label>
            <p class=\"control\">
                ";
            // line 15
            if ((twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "type", array()) == "select")) {
                // line 16
                echo "                    <div class=\"select\">
                    <select name=\"";
                // line 17
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "name", array()), "html", null, true);
                echo "\"";
                if (twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "required", array())) {
                    echo " required";
                }
                echo ">
                        <option>Select</option>
                        ";
                // line 19
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "opts", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["opt"]) {
                    // line 20
                    echo "                            <option value=\"";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["opt"], "id", array()), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["opt"], "name", array()), "html", null, true);
                    echo "</option>
                        ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['opt'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 22
                echo "                    </select>
                    </div>

                ";
            } else {
                // line 26
                echo "                    <input class=\"input\" type=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "type", array()), "html", null, true);
                echo "\" placeholder=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "label", array()), "html", null, true);
                echo "\" name=\"";
                echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "name", array()), "html", null, true);
                echo "\"";
                if (twig_get_attribute($this->env, $this->getSourceContext(), $context["input"], "required", array())) {
                    echo " required";
                }
                echo ">

                ";
            }
            // line 29
            echo "            </p>
        </div>
    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['input'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 32
        echo "

    <div class=\"field is-grouped\">
        <p class=\"control\">
            <button class=\"button is-primary\">Register</button>
        </p>
    </div>


</form>
";
    }

    public function getTemplateName()
    {
        return "register.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  112 => 32,  104 => 29,  89 => 26,  83 => 22,  72 => 20,  68 => 19,  59 => 17,  56 => 16,  54 => 15,  49 => 13,  46 => 12,  42 => 11,  36 => 8,  31 => 5,  28 => 4,  11 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "register.twig", "C:\\Users\\Brandtley\\github\\client-portfolio\\app\\views\\register.twig");
    }
}
