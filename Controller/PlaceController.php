<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Place;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class PlaceController extends ThemeController {

    protected $class = "Outlandish\\SiteBundle\\PostType\\Place";

    /**
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Place:index.html.twig")
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * @param Request $request
     * @param mixed $name
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Place:post.html.twig")
     */
    public function singleAction(Request $request, $name) {
        return parent::singleAction($request, $name);
    }

    protected function getIndexPageId()
    {
        return Place::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Place::postType());
    }

    public function postTypes()
    {
        return array(Place::postType());
    }
}