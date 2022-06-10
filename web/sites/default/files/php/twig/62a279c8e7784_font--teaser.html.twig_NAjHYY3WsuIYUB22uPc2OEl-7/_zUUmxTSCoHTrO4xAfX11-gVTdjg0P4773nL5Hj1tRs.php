<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/fontyourface/templates/font--teaser.html.twig */
class __TwigTemplate_6dcb21a7c95260e7773aace4e3c2c24e4f8e633fc53d015b657ef9562fa785d1 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 18
        echo "<div";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 18, $this->source), "html", null, true);
        echo ">
  <div class=\"fontyourface-header\">
    <h4>";
        // line 20
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["font_title"] ?? null), 20, $this->source), "html", null, true);
        echo "</h4>
    <h5>";
        // line 21
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["content"] ?? null), "pid", [], "any", false, false, true, 21), 21, $this->source), "html", null, true);
        echo "</h5>
  </div>
  <div class=\"fontyourface-preview\">
    ";
        // line 24
        if (($context["font_preview"] ?? null)) {
            // line 25
            echo "      <span class=\"fontyourface-preview\" style=\"line-height: 40px\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["font_preview"] ?? null), 25, $this->source), "html", null, true);
            echo "</span>
    ";
        } else {
            // line 27
            echo "      <span class=\"fontyourface-preview\" style=\"";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["font_style_inline"] ?? null), 27, $this->source), "html", null, true);
            echo " font-size: 40px; line-height: 40px\">AaGg</span>
    ";
        }
        // line 29
        echo "  </div>
  <div class=\"fontyourface-operations\">
    ";
        // line 31
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["operation_links"] ?? null), 31, $this->source), "html", null, true);
        echo "
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "modules/contrib/fontyourface/templates/font--teaser.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 31,  69 => 29,  63 => 27,  57 => 25,  55 => 24,  49 => 21,  45 => 20,  39 => 18,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/fontyourface/templates/font--teaser.html.twig", "/var/www/html/web/modules/contrib/fontyourface/templates/font--teaser.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 24);
        static $filters = array("escape" => 18);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
