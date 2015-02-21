<?php

namespace Outlandish\AcadOowpBundle\TemplateComponent;

use Outlandish\AcadOowpBundle\Breadcrumb\BreadcrumbTrail;
use Outlandish\AcadOowpBundle\Repository\Repository;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbComponent implements TemplateComponent
{
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var BreadcrumbTrail
     */
    private $helper;
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param RequestStack    $requestStack
     * @param BreadcrumbTrail $helper
     * @param Repository    $repository
     */
    public function __construct(RequestStack $requestStack, BreadcrumbTrail $helper, Repository $repository)
    {
        $this->requestStack = $requestStack;
        $this->helper = $helper;
        $this->repository = $repository;
    }

    /**
     * Gets all the arguments managed by this component and returns them as an array
     *
     * @return array
     */
    public function getArguments()
    {
        $request = $this->requestStack->getCurrentRequest();
        $id = $request->get('post_id');
        $post = $this->repository->fetchOne($id);
        return [
            'breadcrumbs' => $this->helper->make($post)
        ];
    }


}
