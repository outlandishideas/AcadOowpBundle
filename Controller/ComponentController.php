<?php

namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\TemplateComponent\TemplateComponent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class ComponentController
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @param string
     */
    private $template;

    /**
     * @var TemplateComponents[]
     */
    private $components = [];

    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * Sets the template property on the object
     *
     * @param $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Adds a component to the components property
     *
     * @param TemplateComponent $component
     */
    public function addComponent(TemplateComponent $component)
    {
        $this->components[] = $component;
    }

    private function getArguments()
    {
        $arguments = [];
        foreach ($this->components as $component) {
            $arguments = array_merge($arguments, $component->getArguments());
        }

        return $arguments;
    }

    public function render()
    {
        return $this->templating->renderResponse($this->template, $this->getArguments());
    }
}
