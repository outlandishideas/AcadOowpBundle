<?php

namespace spec\Outlandish\AcadOowpBundle\Manager;

use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\OowpSearchBundle\Form\Type\WPQueryType;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;


class ItemSearchManagerSpec extends ObjectBehavior
{
    public function let(
        FormBuilderInterface $formBuilder,
        FormInterface $form,
        PostManager $postManager)
    {
        $this->beConstructedWith($formBuilder, $postManager);
        $formBuilder->add(Argument::cetera())->willReturn($formBuilder);
        $formBuilder->addEventSubscriber(Argument::any())->willReturn($formBuilder);
        $formBuilder->getForm()
            ->willReturn($form);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Outlandish\AcadOowpBundle\Manager\ItemSearchManager');
    }

    public function it_creates_a_symfony2_form(FormBuilderInterface $formBuilder, FormInterface $form)
    {
        $this->create()->shouldReturnAnInstanceOf('Symfony\Component\Form\FormInterface');
    }

    public function it_adds_a_post_type_field_to_the_form(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('post_type', 'post_type', Argument::type('array'))
            ->shouldBeCalled();
        $this->create();
    }

    public function it_adds_an_order_type_field_to_the_form(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('order', 'order', Argument::type('array'))
            ->shouldBeCalled();
        $this->create();
    }

    public function it_adds_an_order_by_type_field_to_the_form(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('order_by', 'order_by', Argument::type('array'))
            ->shouldBeCalled();
        $this->create();
    }

    public function it_uses_default_post_type_choices_if_no_post_types_are_provided(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('post_type', 'post_type', ['choices' => ['post' => 'Post']])
            ->shouldBeCalled();
        $this->create();
    }

    public function it_uses_default_order_by_choices_if_no_order_types_are_provided(FormBuilderInterface $formBuilder)
    {
        $formBuilder
            ->add('order_by', 'order_by', [])
            ->shouldBeCalled();
        $this->create();
    }

    public function it_uses_choices_provided_by_user_for_post_types(FormBuilderInterface $formBuilder)
    {
        $choices = ['blog' => 'Blog'];
        $formBuilder
            ->add('post_type', 'post_type', ['choices' => $choices])
            ->shouldBeCalled();
        $this->create($choices);
    }

    public function it_uses_choices_provided_by_the_user_for_order_by(FormBuilderInterface $formBuilder)
    {
        $choices = ['title' => 'Title'];
        $formBuilder
            ->add('order_by', 'order_by', ['choices' => $choices])
            ->shouldBeCalled();
        $this->create(null, $choices);
    }

    public function it_adds_an_event_subscriber_to_the_form(FormBuilderInterface $formBuilder)
    {
        $subscriber = 'Outlandish\OowpSearchBundle\Form\EventSubscriber\WPFormEventSubscriber';
        $formBuilder
            ->addEventSubscriber(Argument::type($subscriber))
            ->shouldBeCalled();
        $this->create();
    }
}
