<?php

namespace Outlandish\AcadOowpBundle\Manager;

use Outlandish\OowpBundle\Manager\PostManager;
use Outlandish\OowpSearchBundle\Form\EventSubscriber\WPFormEventSubscriber;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Creates a form that produces WP Query arguments
 *
 * Call the create method and pass through the $postTypes and $
 *
 * Class ItemSearchManager
 * @package Outlandish\AcadOowpBundle\Manager
 */
class ItemSearchManager
{
    /**
     * @var FormBuilderInterface
     */
    private $formBuilder;
    /**
     * @var PostManager
     */
    private $postManager;

    /**
     * @param FormBuilderInterface $formBuilder
     */
    public function __construct(FormBuilderInterface $formBuilder, PostManager $postManager)
    {
        $this->formBuilder = $formBuilder;
        $this->postManager = $postManager;
    }

    /**
     * @param null|array $postTypes override the default choices for the post_type field
     * @return WPQueryType
     */
    public function create($postTypes = null, $orderBy = null)
    {
        if($postTypes === null){
            $postTypes = ['post' => 'Post'];
        }

        $this->formBuilder
            ->add('post_type', 'post_type',
                $this->postTypeOptions($postTypes));
        $this->formBuilder
            ->add('order', 'order', []);
        $this->formBuilder
            ->add('order_by', 'order_by',
                $this->orderByOptions($orderBy));
        $this->formBuilder
            ->addEventSubscriber(new WPFormEventSubscriber());
        $form = $this->formBuilder->getForm();

        return $form;
    }

    /**
     * @param $orderBy
     * @return array
     */
    private function orderByOptions($orderBy)
    {
        $options = [];
        if ($orderBy !== null) {
            $options = ['choices' => $orderBy];
        }
        return $options;
    }

    /**
     * @param $orderBy
     * @return array
     */
    private function postTypeOptions($postTypes)
    {
        $options = [];
        if ($postTypes !== null) {
            $options = ['choices' => $postTypes];
        }
        return $options;
    }
}
