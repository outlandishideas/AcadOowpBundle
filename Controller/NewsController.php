<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\ResourceController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NewsController extends BaseController {

    /**
     * @Route("/news/", name="newsIndex")
     * @Template("OutlandishAcadOowpBundle:News:newsIndex.html.twig")
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request) {
        $response = array();

        $post = $this->querySingle(array('page_id' => News::postTypeParentId()));

        $response = $this->indexResponse($post, $request, array(News::postType()));
        return $response;
    }

    /**
     * @Route("/news/{name}/", name="newsPost")
     * @Template("OutlandishAcadOowpBundle:News:newsPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => News::postType()));

        $sideItems = array(
            $post->connectedPeople('Authors'),
            $post->connectedThemes(),
            $post->connectedDocuments(),
            $post->connectedEvents(),
            $post->connectedPlaces(),
        );

        $response['post'] = $post;
        $response['sideItems'] = $sideItems;
        return $response;
    }

}