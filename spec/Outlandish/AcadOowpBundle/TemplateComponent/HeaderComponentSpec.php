<?php

namespace spec\Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\OowpBundle\Helper\WordpressHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HeaderComponentSpec extends ObjectBehavior
{
    public function let(WordpressHelper $helper)
    {
        $this->beConstructedWith($helper);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\TemplateComponent\HeaderComponent');
    }

    public function it_returns_an_array()
    {
        $this->getArguments()->shouldBeArray();
    }

    public function it_gets_the_header_image_and_the_header_text(WordpressHelper $helper)
    {
        $helper->acfOption('header_image')->shouldBeCalled();
        $helper->acfOption('header_text')->shouldBeCalled();
        $this->getArguments();
    }
}
