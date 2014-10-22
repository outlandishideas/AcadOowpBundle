<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\SiteBundle\PostType\Document;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class DocumentController extends ResourceController {

    /**
     * action for index
     *
     * @param Request $request
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Document:index.html.twig")
     */
    public function indexAction(Request $request) {
        return parent::indexAction($request);
    }

    /**
     * @param Request $request
     * @param mixed $name
     * @return array
     *
     * @Template("OutlandishAcadOowpBundle:Document:post.html.twig")
     */
    public function singleAction(Request $request, $name)
    {
        $response =  parent::singleAction($request, $name);
        $response['documentUrl'] = $response['post']->documentUrl();
        $response['attachment'] = $response['post']->attachment();
        return $response;
    }

    protected function getIndexPageId()
    {
        return Document::postTypeParentId();
    }

    protected function getSearchResultPostTypes()
    {
        return array(Document::postType());
    }

    public function postTypes()
    {
        return array(Document::postType());
    }
}