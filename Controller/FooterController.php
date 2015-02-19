<?php
/**
 * Created by PhpStorm.
 * User: Matthew
 * Date: 18/02/2015
 * Time: 19:57
 */

namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class FooterController {

    /**
     * @var EngineInterface
     */
    private $engine;
    /**
     * @var TemplateBuilderInterface
     */
    private $footerBuilder;

    public function __construct(EngineInterface $engine, TemplateBubilderInterface $uilder)
    {
        $this->engine = $engine;
        $this->builder = $builder;
    }

    public function render()
    {
        $templateArguments = $this->footerBuilder->arguments();
        $template = $this->footerBuilder->template();
        $this->engine->renderResponse($template, $templateArguments);
    }

}