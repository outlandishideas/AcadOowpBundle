<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NewsController extends ResourceController {

    /**
     * @Template("OutlandishAcadOowpBundle:News:index.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * @param Request $request
     * @param mixed $name
     * @return array
     * @Template("OutlandishAcadOowpBundle:News:post.html.twig")
     */
    public function singleAction(Request $request, $name) {
        return parent::singleAction($request, $name);
    }

    protected function getIndexPageId()
    {
        return News::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(News::postType());
    }

    public function postTypes()
    {
        return array(News::postType());
    }

}