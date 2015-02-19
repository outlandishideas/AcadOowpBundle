<?php

namespace spec\Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\TemplateComponent\TemplateComponent;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class ComponentControllerSpec extends ObjectBehavior
{
    public function let(EngineInterface $templating)
    {
        $this->beConstructedWith($templating);
        $this->setTemplate('a.template');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\Controller\ComponentController');
    }

    public function it_allows_you_to_set_a_template()
    {
        $this->setTemplate('a.template');
    }

    public function it_allows_you_to_add_a_template_component_to_it(TemplateComponent $component)
    {
        $this->addComponent($component);
    }

    public function it_renders_a_response_using_the_templating_engine(EngineInterface $templating)
    {
        $templating->renderResponse(Argument::type('string'), Argument::type('array'))->shouldBeCalled();
        $this->render();
    }

    public function it_sets_the_template_of_the_response(EngineInterface $templating)
    {
        $templating->renderResponse('a.template', Argument::type('array'))->shouldBeCalled();
        $this->render();
    }

    public function it_merges_all_arguments_from_the_template_components(EngineInterface $templating, TemplateComponent $componentA, TemplateComponent $componentB )
    {
        $this->addComponent($componentA);
        $componentA->getArguments()->willReturn(['componentA']);
        $templating->renderResponse(Argument::type('string'), ['componentA'])->shouldBeCalled();
        $this->render();

        $this->addComponent($componentB);
        $componentB->getArguments()->willReturn(['componentB']);
        $templating->renderResponse(Argument::type('string'), ['componentA', 'componentB'])->shouldBeCalled();
        $this->render();
    }
}
