<?php


namespace Outlandish\AcadOowpBundle\Controller;


use Outlandish\AcadOowpBundle\Controller\DefaultController as BaseController;
use Outlandish\AcadOowpBundle\AcaSearch\AcaSearch;
use Outlandish\SiteBundle\PostType\News;
use Outlandish\SiteBundle\PostType\Person;
use Outlandish\SiteBundle\PostType\Role;
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

        /** @var AcaSearch $search */
        //todo: meed to sort our faceted search first
//        $search = $this->container->get('aca.search');

        $items = News::fetchAll();

        $sideItems = array(
            array(
                'title' => 'People',
                'items' => Person::fetchAll(array('posts_per_page' => 3))
            ),
            array(
                'title' => 'Roles',
                'items' => Role::fetchAll(array('posts_per_page' => 3))
            )
        );

        $response['post'] = $post;
        $response['items'] = $items;
        $response['sections'] = $post->sections();
        $response['sideItems'] = $sideItems;
        return $response;
    }

    /**
     * @Route("/news/{name}/", name="newsPost")
     * @Template("OutlandishAcadOowpBundle:News:newsPost.html.twig")
     */
    public function singleAction($name) {
        $response = array();

        $post = $this->querySingle(array('name' => $name, 'post_type' => News::postType()));

        $response['post'] = $post;
        return $response;
    }

}