<?php

namespace spec\Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\AcadOowpBundle\PageSections\PageSectionsBuilder;
use Outlandish\OowpBundle\Helper\WordpressHelper;
use Outlandish\AcadOowpBundle\Helper\WordpressMenuHelper;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FooterComponentSpec extends ObjectBehavior
{
    /**
     * It is constructed with WordpressHelper, WordpressMenuHelper and PageSectionsBuilder
     */
    public function let(WordpressHelper $wpHelper, WordPressMenuHelper $wpMenuHelper, PageSectionsBuilder $sectionBuilder)
    {
        $this->beConstructedWith($wpHelper, $wpMenuHelper, $sectionBuilder);
    }

    /**
     * It can be initialised and is of the correct type.
     */
    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\TemplateComponent\FooterComponent');
    }

    /**
     * It returns an array of arguments
     */
    public function it_returns_an_array_of_arguments()
    {
        $this->getArguments()->shouldBeArray();
    }

}
