<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\AcaSearch\AcaSearch;
use Outlandish\AcadOowpBundle\PostType\Post;
use Outlandish\SiteBundle\PostType\News;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NewsController extends BaseController {

    /**
     * @Route("/news/", name="newsIndex")
     * @Template("OutlandishAcadOowpBundle:News:newsIndex.html.twig")
     */
    public function indexAction() {
        $response = array();

        $post = $this->querySingle(array('page_id' => News::postTypeParentId()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        /** @var AcaSearch $search */
        //todo: meed to sort our faceted search first
//        $search = $this->container->get('aca.search');

        $people = News::fetchAll();

        $response['post'] = $post;
        $response['items'] = $people;
        return $response;
    }

    /**
     * @Route("/news/{name}/", name="newsPost")
     * @Template("OutlandishAcadOowpBundle:News:newsPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => News::postType()));
        if(!$post) return $this->redirect($this->generateUrl("home"));

        $response['post'] = $post;
        return $response;
    }

}