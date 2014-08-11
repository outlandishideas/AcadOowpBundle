<?php


namespace Outlandish\AcadOowpBundle\Controller;

use Outlandish\AcadOowpBundle\PostType\Document;
use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Outlandish\RoutemasterBundle\Annotation\Template;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController {

    /**
     * @Route("/search/", name="search")
     * @Template("OutlandishAcadOowpBundle:Search:search.html.twig")
     */
    public function indexAction(Request $request)
    {
        $response = $this->items($request);
        return $response;
    }

    /**
     * @Route("/search/ajax/", name="searchAjax")
     * @Template("OutlandishAcadOowpBundle:Search:searchAjax.html.twig")
     */
    public function ajaxAction(Request $request)
    {
        $response = $this->items($request);
        return $response;
    }

}